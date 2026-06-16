<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Device;
use App\Models\DeviceCommand;
use App\Models\Setting;
use Carbon\Carbon;

class SettingsController extends Controller
{
    public function index()
    {
        $devices = Device::where('status', 'approved')->get();
        $calcMode = Setting::where('key', 'calc_mode')->value('value') ?? 'calculated';
        return view('settings.index', compact('devices', 'calcMode'));
    }

    private function encodeZkTime($datetime) {
        $time = Carbon::parse($datetime);
        return (($time->year - 2000) * 12 * 31 + (($time->month - 1) * 31) + $time->day - 1) * (24 * 60 * 60) + ($time->hour * 60 * 60) + ($time->minute * 60) + $time->second;
    }

    public function updateTime(Request $request)
    {
        $request->validate([
            'device_id' => 'required',
            'sync_type' => 'required|in:auto,manual',
            'manual_time' => 'required_if:sync_type,manual|date',
        ]);

        $deviceId = $request->device_id;
        
        $carbonTime = $request->sync_type === 'auto' ? Carbon::now() : Carbon::parse($request->manual_time);
        
        // ZKTeco Custom Encoded Time
        $zkTimestamp = $this->encodeZkTime($carbonTime);
        // Standard String Format
        $stringFormat = $carbonTime->format('Y-m-d H:i:s');
        // Unix Timestamp
        $unixTimestamp = $carbonTime->timestamp;

        $commandsToQueue = [
            "SET OPTIONS DateTime={$zkTimestamp}",
            "SET OPTIONS DateTime={$stringFormat}",
            "SET OPTIONS DateTime={$unixTimestamp}",
        ];

        if ($deviceId === 'all') {
            $devices = Device::where('status', 'approved')->get();
            foreach ($devices as $device) {
                // Clear any pending time commands to avoid conflicts
                DeviceCommand::where('device_id', $device->id)
                    ->where('executed', false)
                    ->where('command', 'LIKE', 'SET OPTIONS DateTime%')
                    ->delete();
                    
                foreach ($commandsToQueue as $cmd) {
                    DeviceCommand::create([
                        'device_id' => $device->id,
                        'command' => $cmd,
                    ]);
                }
            }
        } else {
            // Clear any pending time commands
            DeviceCommand::where('device_id', $deviceId)
                ->where('executed', false)
                ->where('command', 'LIKE', 'SET OPTIONS DateTime%')
                ->delete();

            foreach ($commandsToQueue as $cmd) {
                DeviceCommand::create([
                    'device_id' => $deviceId,
                    'command' => $cmd,
                ]);
            }
        }

        return back()->with('success', 'Time synchronization command sent to device(s).');
    }

    public function updateLoggingMode(Request $request)
    {
        $request->validate([
            'calc_mode' => 'required|in:calculated,device',
        ]);

        Setting::updateOrCreate(
            ['key' => 'calc_mode'],
            ['value' => $request->calc_mode]
        );

        return back()->with('success', 'Strategy loaded successfully');
    }

    public function updateWallpaper(Request $request)
    {
        $request->validate([
            'device_id' => 'required',
            'wallpaper' => 'required|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        $deviceId = $request->device_id;
        $image = $request->file('wallpaper');

        $imageContent = file_get_contents($image->getRealPath());
        $base64 = base64_encode($imageContent);

        $command = "SET OPTIONS Wallpaper={$base64}";

        if ($deviceId === 'all') {
            $devices = Device::where('status', 'approved')->get();
            foreach ($devices as $device) {
                DeviceCommand::where('device_id', $device->id)
                    ->where('executed', false)
                    ->where('command', 'LIKE', 'SET OPTIONS Wallpaper%')
                    ->delete();

                DeviceCommand::create([
                    'device_id' => $device->id,
                    'command' => $command,
                ]); 
            }
        } else {
            DeviceCommand::where('device_id', $deviceId)
                ->where('executed', false)
                ->where('command', 'LIKE', 'SET OPTIONS Wallpaper%')
                ->delete();

            DeviceCommand::create([
                'device_id' => $deviceId,
                'command' => $command,
            ]);
        }

        return back()->with('success', 'Wallpaper update command sent to device(s). The device will update its background on next sync.');
    }
}
