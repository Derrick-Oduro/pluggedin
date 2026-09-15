<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
class SellerVerificationController extends Controller { public function index() { $sellers=User::whereHas('uploadedProducts')->withCount('uploadedProducts')->latest()->paginate(20); return view('superadmin.sellers.index',compact('sellers')); } public function update(Request $request, User $seller) { abort_unless($seller->uploadedProducts()->exists(), 422); $seller->update(['seller_verified_at' => $seller->seller_verified_at ? null : now()]); return back()->with('success', 'Seller verification updated.'); } }
