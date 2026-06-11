<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Device;
use App\Models\DeviceCommand;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class AdmsController extends Controller
{
    private function handleDeviceSeen($sn)
    {
        if (!$sn) return null;
        $device = Device::where('sn', $sn)->first();
        if ($device) {
            $device->update(['last_seen' => time()]);
        } else {
            // Auto create device
            $device = Device::create([
                'sn' => $sn,
                'name' => 'SenseFace-' . $sn,
                'last_seen' => time(),
            ]);
        }
        return $device;
    }

    public function handshake(Request $request)
    {
        $sn = $request->query('SN');
        Log::info("[ADMS] Handshake from SN: " . $sn);
        $this->handleDeviceSeen($sn);
        return response('OK', 200)->header('Content-Type', 'text/plain');
    }

    public function getRequests(Request $request)
    {
        $sn = $request->query('SN');
        $device = $this->handleDeviceSeen($sn);

        if (!$device) {
            return response('OK', 200)->header('Content-Type', 'text/plain');
        }

        $commands = DeviceCommand::where('device_id', $device->id)
            ->where('synced', false)
            ->get();

        if ($commands->count() > 0) {
            $cmdString = "";
            foreach ($commands as $cmd) {
                $cmdString .= "C:" . $cmd->id . ":" . $cmd->command . "\n";
                $cmd->update(['synced' => true]);
            }
            Log::info("[ADMS] Sending commands to device:\n" . $cmdString);
            return response($cmdString, 200)->header('Content-Type', 'text/plain');
        }

        return response('OK', 200)->header('Content-Type', 'text/plain');
    }

    public function deviceCmd(Request $request)
    {
        $sn = $request->query('SN');
        $this->handleDeviceSeen($sn);
        
        $body = $request->getContent();
        Log::info("[ADMS] Command executed status from device: \n" . $body);

        $lines = explode("\n", trim($body));
        foreach ($lines as $line) {
            if (preg_match('/ID=(\d+)/', $line, $matches) && str_contains($line, 'Return=0')) {
                $id = $matches[1];
                $cmd = DeviceCommand::find($id);
                if ($cmd) {
                    $cmd->update(['executed' => true]);
                    Log::info("[ADMS] Command Completed: " . $cmd->command);
                    
                    // If it was an ENROLL_FP command, we can queue a BIODATA fetch
                    if (str_contains($line, 'CMD=ENROLL_FP')) {
                        DeviceCommand::create(['device_id' => $cmd->device_id, 'command' => 'DATA QUERY BIODATA']);
                        DeviceCommand::create(['device_id' => $cmd->device_id, 'command' => 'DATA QUERY templatev10']);
                    }
                }
            }
        }

        return response('OK', 200)->header('Content-Type', 'text/plain');
    }

    public function receiveData(Request $request)
    {
        $sn = $request->query('SN');
        $table = $request->query('table');
        $device = $this->handleDeviceSeen($sn);
        
        $body = $request->getContent();
        Log::info("[ADMS] Raw Data for " . $table . "\n" . substr($body, 0, 500));

        if (!$device || !$body) {
            return response('OK', 200)->header('Content-Type', 'text/plain');
        }

        $lines = array_filter(array_map('trim', explode("\n", $body)));

        if ($table === 'ATTLOG') {
            foreach ($lines as $line) {
                $parts = explode("\t", $line);
                if (count($parts) >= 2) {
                    $userId = trim($parts[0]);
                    $timestamp = trim($parts[1]);
                    $status = isset($parts[2]) ? (int)trim($parts[2]) : 0;

                    Attendance::updateOrCreate(
                        ['user_id' => $userId, 'timestamp' => $timestamp, 'device_id' => $device->id],
                        ['user_name' => 'Unknown (ADMS)', 'status' => $status]
                    );
                }
            }
        } 
        else if ($table === 'USER' || $table === 'OPERLOG' || strtoupper($table) === 'USERINFO') {
            foreach ($lines as $line) {
                if (str_starts_with($line, 'BIOPHOTO') || str_starts_with($line, 'USERPIC')) {
                    preg_match('/\bPIN=([^\t\s]+)/i', $line, $userIdMatch);
                    if (!$userIdMatch) preg_match('/\bPin=([^\t\s]+)/i', $line, $userIdMatch);
                    $userId = $userIdMatch ? trim($userIdMatch[1]) : null;
                    
                    preg_match('/Content=(.*)/i', $line, $contentMatch);
                    if (!$contentMatch) preg_match('/TMP=(.*)/i', $line, $contentMatch);
                    $content = $contentMatch ? trim($contentMatch[1]) : null;

                    if ($userId && $content && str_starts_with($line, 'USERPIC')) {
                        User::where('user_id', $userId)->update([
                            'photo' => $content,
                            'last_sync_time' => time()
                        ]);
                    }
                    continue;
                }
                
                if (str_starts_with($line, 'BIODATA') || str_starts_with($line, 'templatev10') || str_starts_with($line, 'fingrtmp')) {
                    // Similar logic for inline biodata ...
                    continue; 
                }

                preg_match('/\bPIN=([^\t\s]+)/i', $line, $userIdMatch);
                $userId = $userIdMatch ? trim($userIdMatch[1]) : null;
                
                preg_match('/\bName=([^\t]+)/i', $line, $nameMatch);
                $name = $nameMatch ? trim($nameMatch[1]) : 'Unknown';
                
                preg_match('/\bPri=([^\t\s]+)/i', $line, $roleMatch);
                $role = $roleMatch ? (int)$roleMatch[1] : 0;
                
                preg_match('/\bPassword=([^\t\s]+)/i', $line, $passMatch);
                if (!$passMatch) preg_match('/\bPasswd=([^\t\s]+)/i', $line, $passMatch);
                $password = $passMatch ? trim($passMatch[1]) : '';
                
                preg_match('/\bCard=([^\t\s]+)/i', $line, $cardMatch);
                $card = $cardMatch ? trim($cardMatch[1]) : '';
                $card = ($card === '0') ? '' : $card;

                if ($userId) {
                    $updateData = [
                        'role' => $role,
                        'password' => $password,
                        'card_number' => $card,
                        'last_sync_time' => time(),
                    ];
                    if ($name !== 'Unknown') $updateData['name'] = $name;

                    User::updateOrCreate(
                        ['user_id' => $userId, 'device_id' => $device->id],
                        $updateData
                    );
                }
            }
        }

        return response('OK', 200)->header('Content-Type', 'text/plain');
    }

    public function testCmd(Request $request)
    {
        $cmd = $request->query('cmd');
        $device = Device::first();
        if ($cmd && $device) {
            DeviceCommand::create(['device_id' => $device->id, 'command' => $cmd]);
            return response("Command queued: $cmd", 200);
        }
        return response("Error", 400);
    }
}
