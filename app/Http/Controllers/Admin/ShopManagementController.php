<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use Illuminate\Http\Request;

class ShopManagementController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending');
        $shops = Shop::with('owner')->when($status !== 'all', fn ($query) => $query->where('status', $status))->latest()->paginate(20)->withQueryString();
        return view('superadmin.shops.index', compact('shops', 'status'));
    }

    public function update(Request $request, Shop $shop)
    {
        $data = $request->validate(['status' => 'required|in:approved,suspended,pending', 'plan' => 'required|in:starter,campus_pro,business', 'admin_notes' => 'nullable|string|max:2000']);
        $shop->update($data);
        return back()->with('success', $shop->name.' has been updated.');
    }
}
