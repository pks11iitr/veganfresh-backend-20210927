<?php

namespace App\Http\Controllers\RiderAdmin;

use App\Models\Order;
use App\Models\Rider;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    public function index(Request $request){
        if(isset($request->search)){
            $orders=Order::where(function($orders) use ($request){

                $orders->where('name', 'like', "%".$request->search."%")
                    ->orWhere('email', 'like', "%".$request->search."%")
                    ->orWhere('mobile', 'like', "%".$request->search."%")
                    ->orWhereHas('customer', function($customer)use( $request){
                        $customer->where('name', 'like', "%".$request->search."%")
                            ->orWhere('email', 'like', "%".$request->search."%")
                            ->orWhere('mobile', 'like', "%".$request->search."%");
                    });
            });

        }else{
            $orders =Order::where('id', '>=', 0)
                ->whereNotNull('rider_id')
                ->where('status', '!=','pending');
        }
        if($request->fromdate)
            $orders=$orders->where('created_at', '>=', $request->fromdate.'00:00:00');

        if($request->todate)
            $orders=$orders->where('created_at', '<=', $request->todate.'23:59:50');

        if($request->status)
            $orders=$orders->where('status', $request->status);

        if($request->payment_status)
            $orders=$orders->where('payment_status', $request->payment_status);

        if($request->ordertype)
            $orders=$orders->orderBy('created_at', $request->ordertype);

        $orders=$orders->orderBy('id', 'desc')->paginate(10);

        return view('rideradmin.order.view',['orders'=>$orders]);

    }

    public function details(Request $request,$id){
        $order =Order::with(['details.entity'])->findOrFail($id);
        return view('rideradmin.order.details',['order'=>$order]);
    }

}
