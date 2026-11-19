<?php

namespace App\Support;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductImages
{
    /**
     * Store uploaded images[] and return the image columns to save.
     * Returns [] when nothing was uploaded, so existing images are kept on edit.
     */
    public static function fromRequest(Request $request, ?Product $replacing = null): array
    {
        if (! $request->hasFile('images')) {
            return [];
        }

        if ($replacing) {
            self::delete($replacing);
        }

        $paths = [];
        foreach ($request->file('images') as $file) {
            $paths[] = ImageOptimizer::store($file, 'products');
        }

        return ['images' => $paths, 'image_path' => $paths[0] ?? null];
    }

    public static function delete(Product $product): void
    {
        $paths = array_unique(array_filter(array_merge($product->images ?: [], [$product->image_path])));

        foreach ($paths as $path) {
            Storage::disk('public')->delete($path);
        }
    }
}
