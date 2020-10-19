<?php

namespace App\Http\Controllers\StoreAdmin;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
//        $therapy_orders=Order::whereHas('details',function($details){
//            $details->where('entity_type', 'App\Models\Therapy');
//        })
//            ->where('status', '!=', 'pending')
//            ->groupBy('status')
//            ->selectRaw('count(*) as count, status')
//            ->get();
//        $therapy_orders_array=[];
//        $total_order=0;
//        foreach($therapy_orders as $o){
//            if(isset($therapy_orders_array[$o->status]))
//                $therapy_orders_array[$o->status]=0;
//            $therapy_orders_array[$o->status]=$o->count;
//            $total_order=$total_order+$o->count??0;
//        }
//
//
//        $therapy_orders_array['total']=$total_order;
        //var_dump($therapy_orders_array);die;

        //var_dump($therapy_orders_array);die;

        $user=auth()->user();

        $product_orders=Order::whereHas('details',function($details){
            $details->where('entity_type', 'App\Models\Product');
        })
            ->whereIn('status', ['confirmed', 'completed', 'dispatched', 'cancelled'])
            ->where('store_id', $user->id)
            ->groupBy('status')
            ->selectRaw('count(*) as count, sum(total_cost) as total_cost, sum(points_used) as points, sum(points_used) as cashback, sum(coupon_discount) as coupon, status')
            ->get();
        $product_orders_array=[];
        $total_order=0;
        //echo '<pre>';
        //var_dump($product_orders);die;
        //echo '<pre>';
        foreach($product_orders as $o){
            //echo $o->count??0;
            if(!isset($product_orders_array[$o->status]))
                $product_orders_array[$o->status]=0;
            $product_orders_array[$o->status]=$o->count??0;
            $total_order=$total_order+($o->count??0);
            //var_dump($therapy_orders_array);
        }


        $product_orders_array['total']=$total_order;


        $product_revenue=Order::whereHas('details',function($details){
            $details->where('entity_type', 'App\Models\Product');
        })
            ->where('store_id', $user->id)
            ->whereIn('status', ['completed'])
            ->groupBy('status')
            ->selectRaw('sum(total_cost) as total_cost, sum(balance_used) as balance, sum(points_used) as cashback, sum(coupon_discount) as coupon, status')
            ->get();

        $revenue_product=[];
        $revenue_product['total']=$product_revenue[0]['total_cost']??0;
        $revenue_product['cashback']=$product_revenue[0]['cashback']??0;
        $revenue_product['balance']=$product_revenue[0]['balance']??0;
        $revenue_product['coupon']=$product_revenue[0]['coupon']??0;
        //return $revenue_product;

        //var_dump($therapy_orders_array);die;

//        $customers=Customer::selectRaw('count(*) as total, status')
//            ->groupBy('status')->get();
//        $customers_array=[];
//        $total_order=0;
//        foreach($customers as $customer){
//            if(isset($customers_array[$customer->status]))
//                $customers_array[$customer->status]=0;
//            $customers_array[$customer->status]=$customer->total;
//            $total_order=$total_order+$customer->total;
//        }
//        $customers_array['total']=$total_order;
        //echo '<pre>';
        //print_r($customers_array);die;


        $product_orders_data=Order::where('status', 'confirmed')
            ->where('created_at', '>=', date('Y').'-01-01 00:00:00')
            ->whereHas('details',function($details){
                $details->where('entity_type', 'App\Models\Product');
            })
            ->select(DB::raw('Month(created_at) as month'), DB::raw('SUM(total_cost) as total_cost'))
            ->groupBy(DB::raw('Month(created_at)'))
            ->orderBy(DB::raw('Month(created_at)'), 'asc')
            ->get();
        $product_sales=[];
        foreach($product_orders_data as $d){
            $product_sales[$d->month]=$d->total_cost;
        }


        $products=Product::count();

        $revenue=[];
        $revenue['product']=$revenue_product;
        $revenue['total']=$revenue_product;
        //var_dump($therapy_orders_array);die;
        return view('store-admin.home', [
            'product'=>$product_orders_array,
            //'customer'=>$customers_array,
            'revenue'=>$revenue,
            'products'=>$products
        ]);
    }
}

