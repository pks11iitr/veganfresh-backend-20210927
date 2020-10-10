<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class SalesController extends Controller
{
    public function index(Request $request){


        $stores=User::where('id', '>', 1)->get();

        $sales=OrderDetail::with(['size', 'entity', 'order']);

        $sales=$sales->whereHas('order', function($order) use($request){

            $order->where('orders.status', 'completed');

            if($request->store_id)
                $order->where('store_id', $request->store_id);
            if($request->fromdate)
                $order->where('delivery_date', '>=', $request->fromdate);
            if($request->todate)
                $order->where('delivery_date', '<=', $request->todate);
        });

        if($request->search){
            $sales=$sales->whereHasMorph('entity', [Product::class], function($entity) use($request){
                $entity->where('name', 'LIKE', '%'.$request->search.'%');
            });
        }

        $sales=$sales->select(DB::raw('sum(quantity) as quantity'), DB::raw('sum(price) as cost'), 'entity_type', 'entity_id', 'size_id')
            ->groupBy('entity_type', 'entity_id', 'size_id');

        if($request->order_by){
            if($request->order_by=='amount-asc'){
                $sales=$sales->orderBy('cost', 'asc');
            }else if($request->order_by=='amount-desc'){
                $sales=$sales->orderBy('cost', 'desc');
            }else if($request->order_by=='quantity-asc'){
                $sales=$sales->orderBy('quantity', 'asc');
            }else if($request->order_by=='quantity-desc'){
                $sales=$sales->orderBy('quantity', 'desc');
            }
        }

        $sales=$sales->paginate(20);

        return view('admin.sales.sales', compact('sales', 'stores'));

    }
}
