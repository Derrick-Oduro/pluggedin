<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ReferralLink;
use Illuminate\Support\Str;

class ReferralController extends Controller
{
    public function store(Product $product)
    {
        if ($product->status !== 'approved') {
            return redirect()->back()->with('error', 'Referral links can only be created for approved products.');
        }

        $link = ReferralLink::firstOrCreate(
            [
                'user_id' => auth()->id(),
                'product_id' => $product->id,
            ],
            [
                'code' => Str::random(16),
            ]
        );

        return redirect()
            ->back()
            ->with('success', 'Referral link is ready.')
            ->with('referral_link', route('products.referrals.track', [$product, $link->code]));
    }

    public function track(Product $product, string $code)
    {
        $link = ReferralLink::where('product_id', $product->id)
            ->where('code', $code)
            ->firstOrFail();

        $link->increment('clicks');
        $link->forceFill(['last_used_at' => now()])->save();

        $referrals = session('referrals', []);
        $referrals[$product->id] = $link->id;
        session(['referrals' => $referrals]);

        return redirect()->route('products.show', $product);
    }
}
