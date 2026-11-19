<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductFormRequest;
use App\Models\Category;
use App\Support\ProductImages;
use App\Models\Product;
use Illuminate\Http\Request;

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

        // Check the limit before validating, so blocked sellers aren't asked to fix fields first.
        if ($user->hasRole('user') && $user->remainingMonthlyUploads() <= 0) {
            return redirect()
                ->route('products.upload.create')
                ->with('error', 'You have reached your monthly upload limit.');
        }

        $request = app(ProductFormRequest::class); // runs validation

        Product::create(array_merge($request->productData(), ProductImages::fromRequest($request), [
            'uploaded_by' => $user->id,
            'is_user_uploaded' => true,
            'status' => 'pending',
        ]));

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

    public function update(ProductFormRequest $request, Product $product)
    {
        $user = auth()->user();

        if ((int) $product->uploaded_by !== (int) $user->id || ! $product->is_user_uploaded) {
            abort(403);
        }

        $data = array_merge($request->productData(), ProductImages::fromRequest($request, $product));

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
        ProductImages::delete($product);
        $product->delete();
        return back()->with('success', 'Listing removed.');
    }
}
