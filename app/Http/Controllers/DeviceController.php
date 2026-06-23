<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Payload;
use Illuminate\Http\Request;


class DeviceController extends Controller
{
    // الدالة الأولى: للرد على الجهاز عند إثبات الوجود (GET)
    public function handshake(Request $request)
    {
        $sn = $request->query('SN');
        
        // تسجيل الجهاز إذا لم يكن موجوداً (سيكون pending افتراضياً بفضل قاعدة البيانات)
        if ($sn) {
            \App\Models\Device::updateOrCreate(
                ['sn' => $sn],
                ['last_seen' => now()->timestamp]
            );
        }

        Payload::create([
            'device_sn' => $sn,
            'endpoint' => "GET /iclock/cdata",
            'content' => "Handshake Request"
        ]);

        $response = response('OK', 200)->header('Content-Type', 'text/plain');
        
        $device = \App\Models\Device::where('sn', $sn)->first();
        if ($device && $device->time_offset != 0) {
            $response->header('Date', gmdate('D, d M Y H:i:s \G\M\T', time() + $device->time_offset));
        }

        return $response;
    }

    // دالة لتفكيك بيانات الجهاز
    private function parseDeviceMetadata($content) {
        $parts = explode(',', $content);
        $data = [];
        foreach ($parts as $part) {
            if (strpos($part, '=') !== false) {
                list($key, $value) = explode('=', $part, 2);
                $data[ltrim($key, '~')] = $value;
            }
        }
        return $data;
    }

    // الدالة الثانية: لاستقبال البصمات والموظفين (POST)
    public function receiveData(Request $request)
    {
        $sn = $request->query('SN');
        $table = $request->query('table');
        $content = $request->getContent();

        $device = \App\Models\Device::where('sn', $sn)->first();

        // تحديث معلومات الهاردوير (حتى لو كان الجهاز قيد الانتظار) لنعرف ما هو هذا الجهاز
        if ($table === 'options' || strpos($content, '~DeviceName') !== false) {
            $metadata = $this->parseDeviceMetadata($content);
            if ($device) {
                $device->update(['metadata' => json_encode($metadata), 'last_seen' => now()->timestamp]);
            }
        }

        // إذا كان الجهاز غير معتمد (pending)، لا تحفظ البصمات والموظفين
        if ($device && $device->status === 'pending') {
            return response('OK', 200)->header('Content-Type', 'text/plain');
        }

        // معالجة بيانات الموظفين (USERINFO أو OPERLOG إذا أرسلها الجهاز كرد على الاستعلام)
        if (in_array(strtolower($table), ['userinfo', 'user', 'operlog', 'userpic', 'biophoto', 'biodata'])) {
            $lines = explode("\n", trim($content));
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line)) continue;
                
                // Handle USERPIC, BIOPHOTO, BIODATA for Photos and Face templates
                if (str_starts_with($line, 'USERPIC') || str_starts_with($line, 'BIOPHOTO') || str_starts_with($line, 'BIODATA') || str_starts_with($line, 'FINGERTMP')) {
                    $cleanedLine = preg_replace('/^(USERPIC|BIOPHOTO|BIODATA|FINGERTMP)\s+/', '', $line);
                    $fields = explode("\t", $cleanedLine);
                    $data = [];
                    foreach ($fields as $field) {
                        $parts = explode('=', $field, 2);
                        if (count($parts) == 2) {
                            $data[strtolower(trim($parts[0]))] = trim($parts[1]);
                        }
                    }
                    
                    $pin = $data['pin'] ?? null;
                    if ($pin && $device) {
                        $user = \App\Models\User::firstOrCreate(
                            ['user_id' => $pin, 'device_id' => $device->id],
                            ['name' => 'Unknown', 'role' => 0]
                        );
                        
                        if (str_starts_with($line, 'USERPIC') && isset($data['content'])) {
                            $user->photo = $data['content'];
                        }
                        if (str_starts_with($line, 'BIOPHOTO')) {
                            $type = isset($data['type']) ? (int)$data['type'] : 9;
                            if ($type == 9) $user->has_face = 1;
                        }
                        if (str_starts_with($line, 'BIODATA')) {
                            // Extract Type
                            preg_match('/Type=(\d+)/', $line, $typeMatch);
                            $type = $typeMatch[1] ?? null;
                            
                            if ($type == 9) {
                                // Face
                                $user->has_face = 1;
                            } elseif ($type == 1) {
                                // Fingerprint
                                preg_match('/No=(\d+)/', $line, $noMatch);
                                $fid = isset($noMatch[1]) ? (int)$noMatch[1] : 0;
                                
                                $fpData = json_decode($user->fingerprints ?: '{"fids": []}', true);
                                if (!is_array($fpData)) $fpData = ['fids' => []];
                                $fids = $fpData['fids'] ?? [];
                                
                                if (!in_array($fid, $fids)) {
                                    $fids[] = $fid;
                                    $user->fingerprints = json_encode(['fids' => array_values(array_unique($fids))]);
                                }
                            }
                        }
                        if (str_starts_with($line, 'FINGERTMP')) {
                            // Parse FID and store in the fids array
                            $fpData = json_decode($user->fingerprints ?: '{"fids": []}', true);
                            if (!is_array($fpData)) $fpData = ['fids' => []];
                            $fids = $fpData['fids'] ?? [];
                            $fid = isset($data['fid']) ? (int)$data['fid'] : 0;
                            
                            if (!in_array($fid, $fids)) {
                                $fids[] = $fid;
                                $user->fingerprints = json_encode(['fids' => array_values(array_unique($fids))]);
                            }
                        }
                        $user->save();
                    }
                    continue; // Skip the regular user parse
                }

                // معالجة بيانات الموظفين العادية
                if (strpos($line, 'PIN=') === false) continue;

                $fields = explode("\t", $line);
                $userData = [];
                foreach ($fields as $field) {
                    $parts = explode('=', $field, 2);
                    if (count($parts) == 2) {
                        $key = strtolower(trim($parts[0]));
                        if ($key === 'user pin') $key = 'pin';
                        $userData[$key] = trim($parts[1]);
                    }
                }
                
                if (isset($userData['pin']) && $device) {
                    $existingUser = \App\Models\User::where('user_id', $userData['pin'])
                                                    ->where('device_id', $device->id)
                                                    ->first();
                                                    
                    $name = !empty($userData['name']) ? $userData['name'] : ($existingUser ? $existingUser->name : 'Unknown');
                    $password = !empty($userData['passwd']) ? $userData['passwd'] : ($existingUser ? $existingUser->password : null);
                    $card = !empty($userData['card']) ? $userData['card'] : ($existingUser ? $existingUser->card_number : null);

                    \App\Models\User::updateOrCreate(
                        ['user_id' => $userData['pin'], 'device_id' => $device->id],
                        [
                            'uid' => $userData['uid'] ?? null,
                            'name' => $name,
                            'role' => isset($userData['pri']) ? (int)$userData['pri'] : 0,
                            'password' => $password,
                            'card_number' => $card
                        ]
                    );
                }
            }
        }
        
        // معالجة بيانات الحضور والانصراف (ATTLOG)
        if (strtoupper($table) === 'ATTLOG') {
            $lines = explode("\n", trim($content));
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line)) continue;

                $parts = explode("\t", $line);
                if (count($parts) >= 3) {
                    $pin = trim($parts[0]);
                    $timestamp = trim($parts[1]);
                    $status = trim($parts[2]);
                    
                    if ($device) {
                        $user = \App\Models\User::where('user_id', $pin)->where('device_id', $device->id)->first();
                        $userName = $user ? $user->name : null;

                        \App\Models\Attendance::firstOrCreate([
                            'user_id' => $pin,
                            'timestamp' => $timestamp,
                            'device_id' => $device->id
                        ], [
                            'user_name' => $userName,
                            'status' => (int)$status
                        ]);
                    }
                }
            }
        }

        // هنا يتم حفظ البيانات الفعلية إذا كان الجهاز معتمداً (approved)
        if (!empty(trim($content))) {
            Payload::create([
                'device_sn' => $sn,
                'endpoint'  => "POST /iclock/cdata?table=" . $table,
                'content'   => $content
            ]);
        }

        if ($device) {
            $device->update(['last_seen' => now()->timestamp]);
        }

        $response = response('OK', 200)->header('Content-Type', 'text/plain');
        if ($device && $device->time_offset != 0) {
            $response->header('Date', gmdate('D, d M Y H:i:s \G\M\T', time() + $device->time_offset));
        }
        return $response;
    }

    // الدالة الثالثة: إرسال الأوامر المعلقة للجهاز (GET)
    public function getRequests(Request $request)
    {
        $sn = $request->query('SN');
        $device = \App\Models\Device::where('sn', $sn)->first();

        if (!$device || $device->status === 'pending') {
            $response = response('OK', 200)->header('Content-Type', 'text/plain');
            if ($device && $device->time_offset != 0) {
                $response->header('Date', gmdate('D, d M Y H:i:s \G\M\T', time() + $device->time_offset));
            }
            return $response;
        }

        // تحديث آخر ظهور
        $device->update(['last_seen' => now()->timestamp]);

        // جلب الأوامر غير المنفذة ولم يتم إرسالها مسبقاً (لتجنب التكرار اللانهائي)
        $commands = \Illuminate\Support\Facades\DB::table('device_commands')
            ->where('device_id', $device->id)
            ->where('executed', false)
            ->where('synced', false)
            ->get();

        if ($commands->isEmpty()) {
            $response = response('OK', 200)->header('Content-Type', 'text/plain');
            if ($device->time_offset != 0) {
                $response->header('Date', gmdate('D, d M Y H:i:s \G\M\T', time() + $device->time_offset));
            }
            return $response;
        }

        // تجميع الأوامر وإرسالها
        $responseBody = "";
        foreach ($commands as $command) {
            // نسق إرسال الأمر: C:<command_id>:<command_content>
            $responseBody .= "C:{$command->id}:{$command->command}\n";
            
            // تحديد أن الأمر تم إرساله (بانتظار الرد)
            \Illuminate\Support\Facades\DB::table('device_commands')
                ->where('id', $command->id)
                ->update(['synced' => true]);
        }

        $response = response(trim($responseBody), 200)->header('Content-Type', 'text/plain');
        if ($device->time_offset != 0) {
            $response->header('Date', gmdate('D, d M Y H:i:s \G\M\T', time() + $device->time_offset));
        }
        return $response;
    }

    public function deviceCmd(Request $request)
    {
        // الجهاز يرسل POST إلى هنا ليخبرنا عن حالة الأوامر التي نفذها
        $content = $request->getContent();
        $sn = $request->query('SN');

        if (!empty(trim($content))) {
            Payload::create([
                'device_sn' => $sn ?? 'unknown',
                'endpoint'  => "POST /iclock/devicecmd",
                'content'   => $content
            ]);
        }
        
        // النص يكون بصيغة: ID=1&Return=0
        $lines = explode("\n", trim($content));
        foreach ($lines as $line) {
            if (preg_match('/ID=(\d+)/', $line, $matches)) {
                $commandId = $matches[1];

                // إذا كان يحتوي على Return=0 فهذا يعني نجاح، وغيره يعني معالجة ولكن قد يكون هناك فشل
                \Illuminate\Support\Facades\DB::table('device_commands')
                    ->where('id', $commandId)
                    ->update(['executed' => true]);
            }
        }

        $response = response('OK', 200)->header('Content-Type', 'text/plain');
        $device = \App\Models\Device::where('sn', $sn)->first();
        if ($device && $device->time_offset != 0) {
            $response->header('Date', gmdate('D, d M Y H:i:s \G\M\T', time() + $device->time_offset));
        }
        return $response;
    }
}


