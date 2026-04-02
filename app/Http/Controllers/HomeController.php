<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Service;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $featuredProducts = Product::approved()->with('category')->latest()->take(6)->get();
        $services = Service::all();

        return view('home', compact('featuredProducts', 'services'));
    }
}
