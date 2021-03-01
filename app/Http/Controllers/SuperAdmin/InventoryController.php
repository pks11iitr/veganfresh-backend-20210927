<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Exports\InventoryExport;
use App\Exports\InventoryQuantityExport;
use App\Exports\ProductsExport;
use App\Models\Product;
use App\Models\Size;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Excel;

class InventoryController extends Controller
{
    public function packet(Request $request){

        //DB::enableQueryLog();
        $sizes=Size::with('product')
            ->whereHas('product', function($product) use($request){
                if($request->search)
                    $product->where('products.name', 'LIKE', "%".$request->search."%");
                $product->where('stock_type', 'packet');
            })
            ->orderBy('product_prices.stock', $request->order_by??'asc');
        if($request->type=='export'){
            $sizes=$sizes->get();
            return Excel::download(new InventoryExport($sizes), 'packet-inventory.xlsx');
        }else{
            $sizes=$sizes->paginate(20);
            //var_dump(DB::getQueryLog());die;
            return view('admin.inventory.packet', compact('sizes'));
        }


    }


    public function quantity(Request $request){
        $products=Product::where('stock_type', 'quantity')
            ->orderBy('stock', $request->order_by??'asc');
        if($request->search)
            $products=$products->where('products.name', 'LIKE', "%".$request->search."%");

        if($request->type=='export'){
            $products=$products->get();
            return Excel::download(new InventoryQuantityExport($products), 'packet-quantity.xlsx');
        }else {
            $products = $products->paginate(20);

            return view('admin.inventory.quantity', compact('products'));
        }
    }
}
