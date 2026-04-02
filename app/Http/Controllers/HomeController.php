<?php

namespace App\Http\Controllers;

use App\Models\DiscountCampaign;
use App\Models\HeroSlide;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $featuredProducts = Product::approved()->with('category')->latest()->take(6)->get();
        $services = Service::all();

        $heroSlides = HeroSlide::active()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['image_url as src', 'alt_text as alt', 'caption'])
            ->map(function ($slide) {
                return [
                    'src' => $slide->src,
                    'alt' => $slide->alt ?: 'Homepage carousel image',
                    'caption' => $slide->caption ?: 'Upgrade. Repair. Perform better.',
                ];
            })
            ->values();

        $activeCampaign = DiscountCampaign::active()
            ->orderByDesc('discount_percent')
            ->orderByDesc('starts_at')
            ->first();

        return view('home', compact('featuredProducts', 'services', 'heroSlides', 'activeCampaign'));
    }
}
