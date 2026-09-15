<?php
namespace App\Http\Controllers;
use App\Models\Product;
use App\Models\ProductReport;
use Illuminate\Http\Request;
class ProductReportController extends Controller { public function store(Request $request, Product $product) { abort_if($product->uploaded_by === $request->user()->id, 403); $data=$request->validate(['reason'=>'required|in:suspicious_listing,incorrect_details,prohibited_item,other','details'=>'nullable|string|max:1000']); ProductReport::updateOrCreate(['product_id'=>$product->id,'reporter_id'=>$request->user()->id], $data); return back()->with('success','Thanks. Your report has been sent to the PluggedIn review team.'); } }
