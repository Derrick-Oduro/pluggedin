<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Support\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
        return view('superadmin.categories.create');
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories',
        ]);

        $category = Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
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
        return view('superadmin.categories.edit', compact('category'));
    }

    /**
     * Update the specified category in storage.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
        ]);

        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
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
    public function destroy(Category $category)
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

