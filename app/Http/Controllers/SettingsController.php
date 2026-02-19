<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        return view('settings.index');
    }

    public function appearance()
    {
        return redirect()->route('settings.index', ['#appearance']);
    }

    public function updateAppearance(Request $request)
    {
        $request->validate([
            'theme' => 'required|in:light,dark,system',
        ]);

        // Store theme preference in session
        session(['theme' => $request->theme]);

        return back()->with('success', 'Appearance settings updated successfully!');
    }
}
