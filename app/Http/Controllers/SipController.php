<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SipController extends Controller
{
    public function index()
    {
        $serverIp = request()->getHost();
        if ($serverIp === 'localhost' || $serverIp === '127.0.0.1') {
            // Try to get actual LAN IP for instruction purposes
            $serverIp = gethostbyname(gethostname());
        }

        return view('settings.sip', compact('serverIp'));
    }
}
