<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ProductCatalogFilter
{
    public static function fromRequest(Request $request): array
    {
        $keyword = trim((string) $request->query('q', ''));
        $category = (string) $request->query('category', '');
        $minPrice = $request->query('min_price');
        $maxPrice = $request->query('max_price');
        $availability = (string) $request->query('availability', 'all');
        $sort = (string) $request->query('sort', 'latest');

        $minPrice = is_numeric($minPrice) ? (float) $minPrice : null;
        $maxPrice = is_numeric($maxPrice) ? (float) $maxPrice : null;

        if ($minPrice !== null && $maxPrice !== null && $minPrice > $maxPrice) {
            [$minPrice, $maxPrice] = [$maxPrice, $minPrice];
        }

        if (! in_array($availability, ['all', 'in_stock', 'out_of_stock'], true)) {
            $availability = 'all';
        }

        if (! in_array($sort, ['latest', 'price_asc', 'price_desc', 'name_asc'], true)) {
            $sort = 'latest';
        }

        return [
            'keyword' => $keyword,
            'category' => $category,
            'min_price' => $minPrice,
            'max_price' => $maxPrice,
            'availability' => $availability,
            'sort' => $sort,
        ];
    }

    public static function apply(Builder $query, array $filters): Builder
    {
        if ($filters['keyword'] !== '') {
            $query->where(function (Builder $builder) use ($filters) {
                $builder->where('name', 'like', '%'.$filters['keyword'].'%')
                    ->orWhere('description', 'like', '%'.$filters['keyword'].'%');
            });
        }

        if ($filters['category'] !== '') {
            $query->whereHas('category', function (Builder $builder) use ($filters) {
                $builder->where('slug', $filters['category']);
            });
        }

        $query->when($filters['min_price'] !== null, fn (Builder $builder) => $builder->where('price', '>=', $filters['min_price']));
        $query->when($filters['max_price'] !== null, fn (Builder $builder) => $builder->where('price', '<=', $filters['max_price']));
        $query->when($filters['availability'] === 'in_stock', fn (Builder $builder) => $builder->where('stock_quantity', '>', 0));
        $query->when($filters['availability'] === 'out_of_stock', fn (Builder $builder) => $builder->where('stock_quantity', '<=', 0));

        if ($filters['sort'] === 'price_asc') {
            $query->orderBy('price');
        } elseif ($filters['sort'] === 'price_desc') {
            $query->orderByDesc('price');
        } elseif ($filters['sort'] === 'name_asc') {
            $query->orderBy('name');
        } else {
            $query->latest();
        }

        return $query;
    }
}
