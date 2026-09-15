<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserProductController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $products = Product::query()
            ->with('category')
            ->where('uploaded_by', $user->id)
            ->where('is_user_uploaded', true)
            ->when($request->filled('status') && $request->input('status') !== 'all', function ($query) use ($request) {
                $query->where('status', $request->input('status'));
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('products.my-uploads', compact('products'));
    }

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

    public function edit(Product $product)
    {
        $user = auth()->user();

        if ((int) $product->uploaded_by !== (int) $user->id || ! $product->is_user_uploaded) {
            abort(403);
        }

        $categories = Category::orderBy('name')->get();

        return view('products.edit-upload', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $user = auth()->user();

        if ((int) $product->uploaded_by !== (int) $user->id || ! $product->is_user_uploaded) {
            abort(403);
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

        $data = $request->only([
            'name',
            'description',
            'price',
            'category_id',
            'stock_quantity',
        ]);

        if ($request->hasFile('images')) {
            $oldImages = $product->images ?: [];
            foreach ($oldImages as $oldImage) {
                Storage::disk('public')->delete($oldImage);
            }

            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }

            $imagePaths = [];
            foreach ($request->file('images') as $file) {
                $imagePaths[] = $file->store('products', 'public');
            }

            $data['images'] = $imagePaths;
            $data['image_path'] = $imagePaths[0] ?? null;
        }

        // Any user update returns the product to moderation queue.
        $data['status'] = 'pending';
        $data['admin_review_comment'] = null;
        $data['reviewed_by'] = null;
        $data['reviewed_at'] = null;

        $product->update($data);

        return redirect()
            ->route('products.upload.index')
            ->with('success', 'Product updated and resubmitted for review.');
    }

    public function changeListingState(Request $request, Product $product)
    {
        abort_unless((int) $product->uploaded_by === (int) $request->user()->id && $product->is_user_uploaded, 403);
        $data = $request->validate(['listing_state' => 'required|in:active,paused,sold']);
        abort_unless($product->status === 'approved', 422);
        $product->update($data);
        return back()->with('success', 'Listing status updated.');
    }

    public function destroy(Product $product)
    {
        abort_unless((int) $product->uploaded_by === (int) auth()->id() && $product->is_user_uploaded, 403);
        foreach ($product->images ?: [] as $image) { Storage::disk('public')->delete($image); }
        if ($product->image_path) { Storage::disk('public')->delete($product->image_path); }
        $product->delete();
        return back()->with('success', 'Listing removed.');
    }
}
