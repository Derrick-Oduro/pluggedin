<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        return view('settings.index');
    }

    public function updateNotifications(Request $request)
    {
        $request->validate([
            'notify_order_updates' => ['nullable', 'boolean'],
            'notify_booking_updates' => ['nullable', 'boolean'],
            'notify_product_updates' => ['nullable', 'boolean'],
        ]);

        $request->user()->update([
            'notify_order_updates' => $request->boolean('notify_order_updates'),
            'notify_booking_updates' => $request->boolean('notify_booking_updates'),
            'notify_product_updates' => $request->boolean('notify_product_updates'),
        ]);

        return back()->with('success', 'Notification preferences updated.');
    }
}
