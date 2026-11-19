<?php

namespace App\Http\Requests;

use App\Models\Category;
use App\Support\ProductSpecs;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Shared validation for every product form (admin, user upload, shop inventory)
 * so all of them ask for and accept the same details.
 */
class ProductFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Each controller does its own ownership checks.
    }

    public function templateKey(): string
    {
        return ProductSpecs::templateKeyFor(Category::find($this->input('category_id')));
    }

    public function rules(): array
    {
        return array_merge([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'price' => ['required', 'numeric', 'min:0'],
            'category_id' => ['required', 'exists:categories,id'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'condition' => ['required', Rule::in(array_keys(ProductSpecs::conditions()))],
            'whats_included' => ['nullable', 'string', 'max:1000'],
            'images' => ['nullable', 'array', 'max:5'],
            'images.*' => ['image', 'max:4096'],
        ], ProductSpecs::rules($this->templateKey()));
    }

    public function attributes(): array
    {
        return array_merge([
            'category_id' => 'category',
            'stock_quantity' => 'stock',
            'whats_included' => "what's included",
            'images.*' => 'image',
        ], ProductSpecs::attributes($this->templateKey()));
    }

    /** Validated product columns, excluding images (handled by ProductImages). */
    public function productData(): array
    {
        $data = collect($this->validated())->only([
            'name', 'description', 'price', 'category_id', 'stock_quantity', 'condition', 'whats_included',
        ])->all();

        $data['specs'] = ProductSpecs::clean($this->templateKey(), $this->input('specs', [])) ?: null;

        return $data;
    }
}
