<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    // auth + superadmin middleware are applied by the superadmin route group.

    public function index()
    {
        $data = [
            'site_title' => Setting::get('site_title', config('app.name', 'PluggedIn')),
            'brand_blurb' => Setting::get('brand_blurb', 'Practical upgrades and thoughtful repairs.'),
            'contact_email' => Setting::get('contact_email', 'info@pluggedin.example'),
            'contact_address' => Setting::get('contact_address', ''),
        ];

        return view('superadmin.settings.index', $data);
    }

    public function update(Request $request)
    {
        $request->validate([
            'site_title' => 'required|string|max:100',
            'brand_blurb' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:150',
            'contact_address' => 'nullable|string|max:255',
        ]);

        Setting::set('site_title', $request->site_title);
        Setting::set('brand_blurb', $request->brand_blurb);
        Setting::set('contact_email', $request->contact_email);
        Setting::set('contact_address', $request->contact_address);

        return redirect()->route('superadmin.settings.index')->with('success', 'General settings updated.');
    }
}
