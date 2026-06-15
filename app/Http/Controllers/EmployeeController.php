<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Device;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    // عرض صفحة إضافة موظف
    public function create()
    {
        $devices = Device::where('status', 'approved')->get();
        return view('employees.create', compact('devices'));
    }

    // حفظ الموظف وإرسال الأمر للجهاز
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|numeric',
            'name' => 'required|string|max:255',
            'role' => 'required|integer',
            'device_id' => 'required|exists:devices,id',
            'password' => 'nullable|string',
            'card_number' => 'nullable|string'
        ]);

        // حفظ في قاعدة البيانات
        $user = new User();
        $user->user_id = $request->user_id;
        $user->name = $request->name;
        $user->role = $request->role;
        $user->device_id = $request->device_id;
        $user->password = $request->password;
        $user->card_number = $request->card_number;
        $user->save();

        // إرسال الأمر للجهاز
        $this->syncUserToDevice($user, 'UPDATE');

        return redirect('/employees')->with('success', 'Employee added and syncing to device...');
    }

    // عرض صفحة التعديل
    public function edit($id)
    {
        $employee = User::findOrFail($id);
        $devices = Device::where('status', 'approved')->get();
        return view('employees.edit', compact('employee', 'devices'));
    }

    // تحديث الموظف
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|integer',
            'device_id' => 'required|exists:devices,id',
            'password' => 'nullable|string',
            'card_number' => 'nullable|string'
        ]);

        $user = User::findOrFail($id);
        $user->name = $request->name;
        $user->role = $request->role;
        $user->device_id = $request->device_id;
        $user->password = $request->password;
        $user->card_number = $request->card_number;
        $user->save();

        $this->syncUserToDevice($user, 'UPDATE');

        return redirect('/employees')->with('success', 'Employee updated and syncing to device...');
    }

    // حذف الموظف
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $this->syncUserToDevice($user, 'DELETE');
        $user->delete();

        return redirect('/employees')->with('success', 'Employee deleted and removed from device.');
    }

    // حذف بصمة الموظف (لإعادة تسجيلها)
    public function deleteFingerprint($id)
    {
        $user = User::findOrFail($id);
        
        $commands = [
            "DATA DELETE FINGERTMP PIN={$user->user_id}",
            "DATA DELETE BIODATA PIN={$user->user_id}\tType=1"
        ];
        
        foreach ($commands as $cmd) {
            DB::table('device_commands')->insert([
                'device_id' => $user->device_id,
                'command' => $cmd,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        $user->fingerprints = null;
        $user->save();

        return redirect('/employees')->with('success', 'Clear ALL fingerprints command queued to device.');
    }

    // حذف بصمة محددة
    public function deleteSpecificFingerprint($id, $fid)
    {
        $user = User::findOrFail($id);
        
        $commands = [
            "DATA DELETE FINGERTMP PIN={$user->user_id}\tFID={$fid}",
            "DATA DELETE BIODATA PIN={$user->user_id}\tType=1\tNo={$fid}"
        ];
        
        foreach ($commands as $cmd) {
            DB::table('device_commands')->insert([
                'device_id' => $user->device_id,
                'command' => $cmd,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // Remove from local fids array
        $fpData = json_decode($user->fingerprints ?: '{"fids": []}', true);
        if (is_array($fpData) && isset($fpData['fids'])) {
            $fids = array_filter($fpData['fids'], function($v) use ($fid) { return $v != $fid; });
            $user->fingerprints = empty($fids) ? null : json_encode(['fids' => array_values($fids)]);
            $user->save();
        }

        return redirect('/employees')->with('success', "Delete command for fingerprint {$fid} queued to device.");
    }

    // دالة مساعدة لتوليد الأوامر
    private function syncUserToDevice($user, $action)
    {
        if ($action === 'UPDATE') {
            $commandStr = "DATA UPDATE USERINFO PIN={$user->user_id}\tName={$user->name}\tPri={$user->role}";
            $commandStr .= "\tPasswd=" . ($user->password ?? '');
            $commandStr .= "\tCard=" . ($user->card_number ?? '');
        } elseif ($action === 'DELETE') {
            $commandStr = "DATA DELETE USERINFO PIN={$user->user_id}";
        }

        DB::table('device_commands')->insert([
            'device_id' => $user->device_id,
            'command' => $commandStr,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    // جلب الموظفين من الأجهزة (مزامنة عكسية)
    public function fetchFromDevice()
    {
        $devices = Device::where('status', 'approved')->get();
        foreach ($devices as $device) {
            DB::table('device_commands')->insert([
                'device_id' => $device->id,
                'command' => "DATA QUERY userinfo",
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        return redirect('/employees')->with('success', 'Sync command sent to devices. Users will appear here automatically when devices respond.');
    }

    public function uploadPhoto(Request $request, $id)
    {
        $request->validate(['photo' => 'required|image|max:500']); // max 500kb
        $user = User::findOrFail($id);
        
        $image = $request->file('photo');
        $imgData = file_get_contents($image->getRealPath());
        $base64 = base64_encode($imgData);
        $size = strlen($imgData);

        $commands = [];

        if ($request->has('upload_as_profile')) {
            $user->photo = $base64;
            $commands[] = [
                'device_id' => $user->device_id,
                'command' => "DATA UPDATE USERPIC PIN={$user->user_id}\tSize={$size}\tContent={$base64}",
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        if ($request->has('upload_as_face')) {
            $user->has_face = 1;
            $commands[] = [
                'device_id' => $user->device_id,
                'command' => "DATA UPDATE BIOPHOTO PIN={$user->user_id}\tType=9\tSize={$size}\tContent={$base64}",
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        $user->save();

        if (count($commands) > 0) {
            DB::table('device_commands')->insert($commands);
        }

        return redirect('/employees')->with('success', 'Photo uploaded and syncing to device.');
    }

    public function deletePhoto($id)
    {
        $user = User::findOrFail($id);
        $user->photo = null;
        $user->save();

        // Send command to delete the profile picture on ZKTeco device
        DB::table('device_commands')->insert([
            'device_id' => $user->device_id,
            'command' => "DATA DELETE USERPIC PIN={$user->user_id}",
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect('/employees')->with('success', 'Profile photo deleted.');
    }

    public function deleteFace($id)
    {
        $user = User::findOrFail($id);
        $user->has_face = 0;
        $user->save();

        // Type=9 means Face for ZKTeco ADMS
        DB::table('device_commands')->insert([
            ['device_id' => $user->device_id, 'command' => "DATA DELETE BIODATA PIN={$user->user_id}\tType=9", 'created_at' => now(), 'updated_at' => now()],
            ['device_id' => $user->device_id, 'command' => "DATA DELETE BIOPHOTO PIN={$user->user_id}\tType=9", 'created_at' => now(), 'updated_at' => now()]
        ]);

        return redirect('/employees')->with('success', 'Face template cleared from device.');
    }

    public function quickCredentials(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->password = $request->password;
        $user->card_number = $request->card_number;
        $user->save();

        $this->syncUserToDevice($user, 'UPDATE');

        return redirect('/employees')->with('success', 'Credentials updated and syncing to device.');
    }

    public function enrollFingerprint(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $fingerIndex = $request->input('finger_index', 0); // Default to thumb (0)

        // Command for ZKTeco devices to start enrollment
        $commandStr = "ENROLL_FP PIN={$user->user_id}\tFID={$fingerIndex}\tRETRY=3\tOVERWRITE=1";
        DB::table('device_commands')->insert([
            'device_id' => $user->device_id,
            'command' => $commandStr,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect('/employees')->with('success', 'Enrollment command sent. Please place your finger on the device sensor.');
    }

    public function uploadPhotoBase64(Request $request, $id)
    {
        $request->validate(['photo_base64' => 'required|string']);
        $user = User::findOrFail($id);
        
        $base64String = $request->input('photo_base64');
        $base64Data = preg_replace('#^data:image/\w+;base64,#i', '', $base64String);
        $binarySize = strlen(base64_decode($base64Data));

        $commands = [];

        if ($request->has('upload_as_profile')) {
            $user->photo = $base64Data;
            $commands[] = [
                'device_id' => $user->device_id,
                'command' => "DATA UPDATE USERPIC PIN={$user->user_id}\tSize={$binarySize}\tContent={$base64Data}",
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        if ($request->has('upload_as_face')) {
            $user->has_face = 1;
            $commands[] = [
                'device_id' => $user->device_id,
                'command' => "DATA UPDATE BIOPHOTO PIN={$user->user_id}\tType=9\tSize={$binarySize}\tContent={$base64Data}",
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        $user->save();

        if (count($commands) > 0) {
            DB::table('device_commands')->insert($commands);
        }

        return redirect('/employees')->with('success', 'Webcam photo captured and syncing to device.');
    }

    public function updateWorkPattern(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->work_pattern_id = $request->input('work_pattern_id') ?: null;
        if ($user->work_pattern_id) {
            $user->monthly_pattern_id = null;
        }
        $user->save();
        
        return redirect('/employees')->with('success', 'Work pattern updated successfully.');
    }
}

