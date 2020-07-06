<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\SuperAdmin\BaseController;

class HomeController extends BaseController
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
        $therapy_orders=Order::whereHas('details',function($details){
            $details->where('entity_type', 'App\Models\Therapy');
        })
        ->where('status', '!=', 'pending')
        ->groupBy('status')
        ->selectRaw('count(*) as total, status')
        ->get();
        $therapy_orders_array=[];
        $total_order=0;
        foreach($therapy_orders as $o){
            if(isset($therapy_orders_array[$o->status]))
                $therapy_orders_array[$o->status]=0;
            $therapy_orders_array[$o->status]=$o->total;
            $total_order=$total_order+$o->total;
        }
        $therapy_orders_array['total']=$total_order;

        $customers=Customer::selectRaw('count(*) as total, status')->groupBy('status')->get();
        $customers_array=[];
        $total_order=0;
        foreach($customers as $customer){
            if(isset($customers_array[$o->status]))
                $customers_array[$o->status]=0;
            $customers_array[$o->status]=$o->total;
            $total_order=$total_order+$o->total;
        }
        return view('admin.home', [
            'therapy'=>$therapy_orders_array,
            'customers'=>$customers_array
        ]);
    }
}
