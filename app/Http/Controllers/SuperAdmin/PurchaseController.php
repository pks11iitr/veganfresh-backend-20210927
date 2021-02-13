<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Exports\PurchaseItemExport;
use App\Models\Product;
use App\Models\PurchaseItem;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

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

        if($request->export)
            return $this->export($purchases);

        $purchases=$purchases->paginate(10);
        return view('admin.purchaseitem.view',['purchases'=>$purchases]);
    }

    public function export($purchases)
    {
        $purchases=$purchases->get();
        return Excel::download(new PurchaseItemExport($purchases), 'purchaseitem.xlsx');
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
            'create_date'=>'required',
            'manufacturer'=>'required',
            'expiry'=>'required',
            'mrp'=>'required',
            'vendor'=>'required'
        ]);

        if($area=PurchaseItem::create([
            'name'=>$request->name,
            'price'=>$request->price,
            'quantity'=>$request->quantity,
            'create_date'=>$request->create_date,
            'manufacturer'=>$request->manufacturer,
            'expiry'=>$request->expiry,
            'mrp'=>$request->mrp,
            'vendor'=>$request->vendor,
            'remarks'=>$request->remarks
        ]))

        {
            return redirect()->route('purchase.list')->with('success', 'purchase has been created');
        }
        return redirect()->back()->with('error', 'purchase create failed');
    }

}
