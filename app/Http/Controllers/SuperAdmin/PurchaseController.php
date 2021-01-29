<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\Product;
use App\Models\PurchaseItem;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PurchaseController extends Controller
{
    public function index(Request $request){
        $purchases=PurchaseItem::where(function($purchases) use($request){
            $purchases->where('name','LIKE','%'.$request->search.'%');
        });

        if($request->fromdate)
            $purchases=$purchases->where('create_date', '>=', $request->fromdate);

        if($request->todate)
            $purchases=$purchases->where('create_date', '<=', $request->todate);

        $purchases=$purchases->paginate(10);
        return view('admin.purchaseitem.view',['purchases'=>$purchases]);
    }

    public function create(Request $request){

        $products=Product::active()
            ->with(['sizeprice'=> function($sizes){
                $sizes->where('isactive', true);
            }])
            ->get();

        return view('admin.purchaseitem.add', compact('products'));
    }

    public function store(Request $request){
        $request->validate([
            'name'=>'required',
            'price'=>'required',
            'quantity'=>'required',
            'create_date'=>'required'
        ]);

        if($area=PurchaseItem::create([
            'name'=>$request->name,
            'price'=>$request->price,
            'quantity'=>$request->quantity,
            'create_date'=>$request->create_date,
        ]))

        {
            return redirect()->route('purchase.list')->with('success', 'purchase has been created');
        }
        return redirect()->back()->with('error', 'purchase create failed');
    }
}
