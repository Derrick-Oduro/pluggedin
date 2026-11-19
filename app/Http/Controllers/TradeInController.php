<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\TradeInRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TradeInController extends Controller
{
    public function create()
    {
        return view('trade-ins.create', ['products' => Product::approved()->orderBy('name')->get(['id', 'name', 'price'])]);
    }
    public function store(Request $request)
    {
        $data = $request->validate(['desired_product_id' => 'nullable|exists:products,id', 'device_type' => 'required|string|max:80', 'brand' => 'required|string|max:80', 'model' => 'required|string|max:120', 'specifications' => 'nullable|string|max:255', 'condition' => 'required|in:excellent,good,fair,poor', 'notes' => 'nullable|string|max:2000', 'images' => 'nullable|array|max:5', 'images.*' => 'image|max:4096']);
        $paths = [];
        foreach ($request->file('images', []) as $image) { $paths[] = \App\Support\ImageOptimizer::store($image, 'trade-ins'); }
        $data['images'] = $paths ?: null;
        $data['user_id'] = $request->user()->id;
        TradeInRequest::create($data);
        return redirect()->route('trade-ins.index')->with('success', 'Your trade-in request has been submitted. PluggedIn will review it and send a quote.');
    }
    public function index(Request $request)
    {
        $requests = TradeInRequest::with('desiredProduct')->where('user_id', $request->user()->id)->latest()->paginate(12);
        return view('trade-ins.index', compact('requests'));
    }
    public function respond(Request $request, TradeInRequest $tradeIn)
    {
        abort_unless((int) $tradeIn->user_id === (int) $request->user()->id, 403);
        abort_unless($tradeIn->status === 'quoted', 422);
        $data = $request->validate(['decision' => 'required|in:accepted,declined']);
        $tradeIn->update(['status' => $data['decision']]);
        return back()->with('success', $data['decision'] === 'accepted' ? 'Trade-in quote accepted. PluggedIn will contact you to arrange inspection and exchange.' : 'Trade-in quote declined.');
    }
}
