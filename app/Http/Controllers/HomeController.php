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

        if ($heroSlides->isEmpty()) {
            $heroSlides = collect([
                [
                    'src' => 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=1600&h=900&fit=crop',
                    'alt' => 'Laptop and tools on a service desk',
                    'caption' => 'Extend your device life with precision upgrades and expert installation.',
                ],
                [
                    'src' => 'https://images.pexels.com/photos/6755074/pexels-photo-6755074.jpeg?auto=compress&cs=tinysrgb&w=1600&h=900&dpr=1',
                    'alt' => 'Technician repairing electronics',
                    'caption' => 'From diagnostics to full performance boosts, every service is done right.',
                ],
                [
                    'src' => 'https://images.unsplash.com/photo-1587825140708-dfaf72ae4b04?w=1600&h=900&fit=crop',
                    'alt' => 'Computer parts and components',
                    'caption' => 'Quality components, honest advice, and support built for the long run.',
                ],
            ]);
        }

        $activeCampaign = DiscountCampaign::active()
            ->orderByDesc('discount_percent')
            ->orderByDesc('starts_at')
            ->first();

        return view('home', compact('featuredProducts', 'services', 'heroSlides', 'activeCampaign'));
    }
}
