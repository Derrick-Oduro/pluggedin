<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\ProductReport;
use Illuminate\Http\Request;
class ProductReportManagementController extends Controller { public function index(Request $request) { $status=$request->query('status','open'); $reports=ProductReport::with(['product','reporter'])->when($status !== 'all', fn($q)=>$q->where('status',$status))->latest()->paginate(20)->withQueryString(); return view('superadmin.product-reports.index',compact('reports','status')); } public function update(Request $request, ProductReport $report) { $data=$request->validate(['status'=>'required|in:resolved,dismissed']); $report->update($data); return back()->with('success','Report updated.'); } }
