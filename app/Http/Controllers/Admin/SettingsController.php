<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\SiteSettings;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    // auth + superadmin middleware are applied by the superadmin route group.

    public function index()
    {
        return view('superadmin.settings.index', ['settings' => SiteSettings::all()]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'site_title' => 'required|string|max:100',
            'brand_blurb' => 'nullable|string|max:255',
            'contact_email' => 'required|email|max:150',
            'contact_phone' => 'nullable|string|max:40',
            'contact_address' => 'nullable|string|max:255',
            'business_hours' => 'nullable|string|max:500',
        ]);

        SiteSettings::save($data);

        return redirect()->route('superadmin.settings.index')->with('success', 'Site settings updated.');
    }
}
