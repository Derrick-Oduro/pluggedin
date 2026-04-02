<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with(['category', 'uploader'])->latest()->paginate(15);

        return view('admin.products.index', compact('products'));
    }

    public function pending()
    {
        $status = request('status', 'pending');

        $products = Product::with(['category', 'uploader'])
            ->where('is_user_uploaded', true)
            ->when($status !== 'all', function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when(request()->filled('category_id'), function ($query) {
                $query->where('category_id', request('category_id'));
            })
            ->when(request()->filled('uploader_id'), function ($query) {
                $query->where('uploaded_by', request('uploader_id'));
            })
            ->when(request()->filled('search'), function ($query) {
                $query->where('name', 'like', '%'.request('search').'%');
            })
            ->latest()
            ->paginate(15);

        $categories = Category::orderBy('name')->get(['id', 'name']);
        $uploaders = User::query()
            ->whereHas('uploadedProducts')
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.products.pending', compact('products', 'categories', 'uploaders', 'status'));
    }

    public function review(Request $request, Product $product)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'admin_review_comment' => 'nullable|string|max:2000|required_if:status,rejected',
        ]);

        $product->update([
            'status' => $request->status,
            'admin_review_comment' => $request->admin_review_comment,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Product review status updated.');
    }

    public function bulkReview(Request $request)
    {
        $data = $request->validate([
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'integer|exists:products,id',
            'status' => 'required|in:approved,rejected',
            'admin_review_comment' => 'nullable|string|max:2000|required_if:status,rejected',
        ]);

        Product::query()
            ->whereIn('id', $data['product_ids'])
            ->where('is_user_uploaded', true)
            ->update([
                'status' => $data['status'],
                'admin_review_comment' => $data['admin_review_comment'] ?? null,
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
            ]);

        return redirect()->back()->with('success', 'Bulk moderation update completed.');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();

        return view('admin.products.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'stock_quantity' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048'
        ]);

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('products', 'public');
        }

        $data['status'] = 'approved';
        $data['reviewed_by'] = auth()->id();
        $data['reviewed_at'] = now();

        Product::create($data);

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product->load('category');

        return view('admin.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $categories = Category::all();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'stock_quantity' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048'
        ]);

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            $data['image_path'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }

        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully!');
    }
}
