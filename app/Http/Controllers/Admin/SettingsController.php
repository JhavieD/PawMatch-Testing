<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->keyBy('key');
        $isMaintenance = app()->isDownForMaintenance();
        return view('admin.settings', compact('settings', 'isMaintenance'));
    }

    public function update(Request $request)
    {
        $data = [
            'site_name' => $request->input('site_name', ''),
            'contact_email' => $request->input('contact_email', ''),
            'email_notifications' => $request->has('email_notifications') ? '1' : '0',
        ];
        foreach ($data as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
        $settings = Setting::all()->keyBy('key');
        $isMaintenance = app()->isDownForMaintenance();
        return view('admin.settings', compact('settings', 'isMaintenance'));
    }
} 