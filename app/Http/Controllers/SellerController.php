<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;

class SellerController extends Controller
{
    public function show(User $seller)
    {
        $products = Product::approved()
            ->where('uploaded_by', $seller->id)
            ->where('is_user_uploaded', true)
            ->with('category')
            ->latest()
            ->paginate(12);

        abort_if($products->isEmpty() && ! $seller->uploadedProducts()->exists(), 404);

        $completedSales = 0;

        return view('sellers.show', compact('seller', 'products', 'completedSales'));
    }
}
