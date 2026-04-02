<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserProductController extends Controller
{
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $user = auth()->user();

        return view('products.upload', [
            'categories' => $categories,
            'limit' => $user->monthlyUploadLimit(),
            'used' => $user->monthlyProductUploadCount(),
            'remaining' => $user->remainingMonthlyUploads(),
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        if ($user->hasRole('user') && $user->remainingMonthlyUploads() <= 0) {
            return redirect()
                ->route('products.upload.create')
                ->with('error', 'You have reached your monthly upload limit.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'stock_quantity' => 'required|integer|min:0',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|max:3072',
        ]);

        $imagePaths = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $imagePaths[] = $file->store('products', 'public');
            }
        }

        Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'category_id' => $request->category_id,
            'stock_quantity' => $request->stock_quantity,
            'uploaded_by' => $user->id,
            'is_user_uploaded' => true,
            'status' => 'pending',
            'image_path' => $imagePaths[0] ?? null,
            'images' => $imagePaths ?: null,
        ]);

        return redirect()
            ->route('user.dashboard')
            ->with('success', 'Product submitted for admin review.');
    }
}
