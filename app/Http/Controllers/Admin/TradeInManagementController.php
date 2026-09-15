<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TradeInRequest;
use Illuminate\Http\Request;

class TradeInManagementController extends Controller
{
    public function index()
    {
        $requests = TradeInRequest::with(['user', 'desiredProduct'])->latest()->paginate(20);
        return view('superadmin.trade-ins.index', compact('requests'));
    }
    public function quote(Request $request, TradeInRequest $tradeIn)
    {
        $data = $request->validate(['quoted_value' => 'required|numeric|min:0', 'admin_notes' => 'nullable|string|max:2000']);
        $tradeIn->update([...$data, 'status' => 'quoted', 'quoted_at' => now()]);
        return back()->with('success', 'Trade-in quote sent to '.$tradeIn->user->name.'.');
    }
}
