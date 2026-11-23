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
        $services = Service::active()->ordered()->get();

        // The premium hero displays one deliberate image rather than rotating
        // through a carousel. Super Admin selects it by making it active and
        // giving it the lowest sort order.
        $heroSlide = HeroSlide::active()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->first();

        $activeCampaign = DiscountCampaign::active()
            ->orderByDesc('discount_percent')
            ->orderByDesc('starts_at')
            ->first();

        return view('home', compact('featuredProducts', 'services', 'heroSlide', 'activeCampaign'));
    }
}
