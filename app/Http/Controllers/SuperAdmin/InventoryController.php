<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\Product;
use App\Models\Size;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

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
            ->orderBy('product_prices.stock', $request->order_by??'asc')
            ->paginate(20);
        //var_dump(DB::getQueryLog());die;
        return view('admin.inventory.packet', compact('sizes'));

    }


    public function quantity(Request $request){
        $products=Product::where('stock_type', 'quantity')
            ->orderBy('stock', $request->order_by??'asc');
        if($request->search)
            $products=$products->where('products.name', 'LIKE', "%".$request->search."%");

        $products=$products->paginate(20);

        return view('admin.inventory.quantity', compact('products'));
    }
}
