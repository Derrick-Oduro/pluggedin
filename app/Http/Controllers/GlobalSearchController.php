<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;

class GlobalSearchController extends Controller
{
    public function index(Request $request)
    {
        $term = trim((string) $request->query('q', ''));
        $productCategory = (string) $request->query('product_category', '');
        $orderStatus = (string) $request->query('order_status', '');
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

        $canSearchOrders = auth()->check();
        $canSearchUsers = auth()->check() && auth()->user()->isSuperAdmin();

        $allowedTypes = ['all', 'products', 'services'];
        if ($canSearchOrders) {
            $allowedTypes[] = 'orders';
        }
        if ($canSearchUsers) {
            $allowedTypes[] = 'users';
        }

        $type = (string) $request->query('type', 'all');
        if (! in_array($type, $allowedTypes, true)) {
            $type = 'all';
        }

        $categories = Category::query()->orderBy('name')->get(['id', 'name', 'slug']);
        $orderStatuses = collect(['pending', 'confirmed', 'completed', 'cancelled']);

        $products = collect();
        $services = collect();
        $orders = collect();
        $users = collect();

        if ($term !== '') {
            if (in_array($type, ['all', 'products'], true)) {
                $productsQuery = Product::query()
                    ->with('category')
                    ->approved()
                    ->when($productCategory !== '', function ($query) use ($productCategory) {
                        $query->whereHas('category', fn ($categoryQuery) => $categoryQuery->where('slug', $productCategory));
                    })
                    ->when($minPrice !== null, fn ($query) => $query->where('price', '>=', $minPrice))
                    ->when($maxPrice !== null, fn ($query) => $query->where('price', '<=', $maxPrice))
                    ->when($availability === 'in_stock', fn ($query) => $query->where('stock_quantity', '>', 0))
                    ->when($availability === 'out_of_stock', fn ($query) => $query->where('stock_quantity', '<=', 0))
                    ->where(function ($query) use ($term) {
                        $query->where('name', 'like', "%{$term}%")
                            ->orWhere('description', 'like', "%{$term}%");
                    });

                if ($sort === 'price_asc') {
                    $productsQuery->orderBy('price');
                } elseif ($sort === 'price_desc') {
                    $productsQuery->orderByDesc('price');
                } elseif ($sort === 'name_asc') {
                    $productsQuery->orderBy('name');
                } else {
                    $productsQuery->latest();
                }

                $products = $productsQuery
                    ->take(6)
                    ->get();
            }

            if (in_array($type, ['all', 'services'], true)) {
                $servicesQuery = Service::query()
                    ->when($minPrice !== null, fn ($query) => $query->where('price', '>=', $minPrice))
                    ->when($maxPrice !== null, fn ($query) => $query->where('price', '<=', $maxPrice))
                    ->where(function ($query) use ($term) {
                        $query->where('name', 'like', "%{$term}%")
                            ->orWhere('description', 'like', "%{$term}%");
                    });

                if ($sort === 'price_asc') {
                    $servicesQuery->orderBy('price');
                } elseif ($sort === 'price_desc') {
                    $servicesQuery->orderByDesc('price');
                } elseif ($sort === 'name_asc') {
                    $servicesQuery->orderBy('name');
                } else {
                    $servicesQuery->latest();
                }

                $services = $servicesQuery
                    ->take(6)
                    ->get();
            }

            if ($canSearchOrders) {
                $user = auth()->user();

                if (in_array($type, ['all', 'orders'], true)) {
                $orders = Order::query()
                    ->with('user')
                    ->when(! $user->isAdmin(), fn ($query) => $query->where('user_id', $user->id))
                    ->when($orderStatus !== '', fn ($query) => $query->where('status', $orderStatus))
                    ->where(function ($query) use ($term) {
                        $query->where('id', 'like', "%{$term}%")
                            ->orWhere('status', 'like', "%{$term}%")
                            ->orWhere('delivery_phone', 'like', "%{$term}%")
                            ->orWhere('applied_discount_code', 'like', "%{$term}%");
                    })
                    ->latest()
                    ->take(6)
                    ->get();
                }

                if ($canSearchUsers && in_array($type, ['all', 'users'], true)) {
                    $users = User::query()
                        ->where(function ($query) use ($term) {
                            $query->where('name', 'like', "%{$term}%")
                                ->orWhere('email', 'like', "%{$term}%");
                        })
                        ->latest()
                        ->take(6)
                        ->get();
                }
            }
        }

        return view('search.index', compact(
            'term',
            'products',
            'services',
            'orders',
            'users',
            'type',
            'productCategory',
            'orderStatus',
            'minPrice',
            'maxPrice',
            'availability',
            'sort',
            'categories',
            'orderStatuses',
            'canSearchOrders',
            'canSearchUsers'
        ));
    }
}
