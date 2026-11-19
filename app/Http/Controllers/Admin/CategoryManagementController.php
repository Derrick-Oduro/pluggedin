<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Support\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Support\ProductSpecs;

class CategoryManagementController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index()
    {
        $categories = Category::withCount('products')->orderBy('name')->paginate(15);

        return view('superadmin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        return redirect()->route('superadmin.categories.index', ['modal' => 'create-category']);
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'spec_template' => ['nullable', Rule::in(array_keys(ProductSpecs::templates()))],
        ]);

        $category = Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'spec_template' => $request->spec_template ?: null,
        ]);

        AuditLogger::log('superadmin.category.created', Category::class, $category->id, [
            'name' => $category->name,
            'slug' => $category->slug,
        ], $request);

        return redirect()->route('superadmin.categories.index')->with('success', 'Category created successfully!');
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(Category $category)
    {
        return redirect()->route('superadmin.categories.index', ['modal' => 'edit-category-' . $category->id]);
    }

    /**
     * Update the specified category in storage.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'spec_template' => ['nullable', Rule::in(array_keys(ProductSpecs::templates()))],
        ]);

        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'spec_template' => $request->spec_template ?: null,
        ]);

        AuditLogger::log('superadmin.category.updated', Category::class, $category->id, [
            'name' => $category->name,
            'slug' => $category->slug,
        ], $request);

        return redirect()->route('superadmin.categories.index')->with('success', 'Category updated successfully!');
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(Request $request, Category $category)
    {
        // Check if category has products
        if ($category->products()->count() > 0) {
            return back()->with('error', 'Cannot delete category with existing products!');
        }

        $categoryId = $category->id;
        $categoryName = $category->name;

        $category->delete();

        AuditLogger::log('superadmin.category.deleted', Category::class, $categoryId, [
            'name' => $categoryName,
        ], $request);

        return redirect()->route('superadmin.categories.index')->with('success', 'Category deleted successfully!');
    }
}
