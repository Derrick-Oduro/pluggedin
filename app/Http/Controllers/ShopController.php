<?php
namespace App\Http\Controllers;
use App\Models\Shop;
use App\Models\Category;
use App\Models\Product;
use App\Http\Requests\ProductFormRequest;
use App\Support\ProductImages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
class ShopController extends Controller {
 public function create() { abort_if(Shop::where('owner_id', auth()->id())->exists(), 403); return view('shops.create'); }
 public function store(Request $request) { $data=$request->validate(['name'=>'required|string|max:120','description'=>'required|string|max:2000','campus'=>'nullable|string|max:120','phone'=>'nullable|string|max:40','plan'=>'required|in:starter,campus_pro,business']); $data['owner_id']=$request->user()->id; $data['slug']=Str::slug($data['name']).'-'.Str::lower(Str::random(5)); Shop::create($data); return redirect()->route('dashboard')->with('success','Your shop application is submitted for review.'); }
 public function show(Shop $shop) { abort_unless($shop->status === 'approved', 404); $products=$shop->products()->approved()->with('category')->latest()->paginate(12); return view('shops.show', compact('shop','products')); }
 public function dashboard(Request $request) { $shop = Shop::withCount('products')->where('owner_id', $request->user()->id)->first(); $products = $shop ? $shop->products()->with('category')->latest()->get() : collect(); return view('shops.dashboard', compact('shop', 'products')); }
 public function createProduct(Request $request) { $shop = $this->approvedShop($request); return view('shops.products.create', ['shop' => $shop, 'categories' => Category::orderBy('name')->get()]); }
 public function storeProduct(ProductFormRequest $request) { $shop = $this->approvedShop($request); Product::create(array_merge($request->productData(), ProductImages::fromRequest($request), ['uploaded_by' => $request->user()->id, 'shop_id' => $shop->id, 'is_user_uploaded' => true, 'status' => 'pending'])); return redirect()->route('shops.dashboard')->with('success', 'Shop product submitted for moderation.'); }
 private function approvedShop(Request $request): Shop { $shop = Shop::where('owner_id', $request->user()->id)->firstOrFail(); abort_unless($shop->status === 'approved', 403); return $shop; }
}
