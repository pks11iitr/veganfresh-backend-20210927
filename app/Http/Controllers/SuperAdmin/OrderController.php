<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    public function index(Request $request){
        $orders=Order::with(['details.entity', 'customer', 'details.clinic'])->where('status', '!=', 'pending')->orderBy('id', 'desc')->paginate(20);

        return view('admin.order.index', compact('orders'));

    }
     public function product(Request $request){
        $orders=Order::with(['details.entity', 'customer', 'details.clinic'])->where('status', '!=', 'pending')->orderBy('id', 'desc')->paginate(20);

        return view('admin.order.product', compact('orders'));

    }
    
     public function therapy_search(Request $request) {
	      $search=$request->input("search");
	      $payment_status=$request->input("payment_status");
	      $status=$request->input("status");
	     // var_dump($status); die;
	      $fromdate=$request->input("fromdate");
	      $todate1=$request->input("todate");
	      $todate = date('Y-m-d', strtotime($todate1. ' + 1 days'));
	      
	   if($payment_status && $search && $status && $fromdate && $todate){
		           $orders=Order::with(['details.entity', 'customer', 'details.clinic'])->whereBetween('created_at', [$fromdate, $todate])->where('payment_status','=',$payment_status)->where('status','=',$status)->where('name','LIKE','%'.$search.'%')->orwhere('mobile','LIKE','%'.$search.'%')->orwhere('email','LIKE','%'.$search.'%')->orwhere('refid','LIKE','%'.$search.'%')->orderBy('id', 'desc')->paginate(20);

		    }elseif($status && $fromdate && $todate){
			$orders=Order::with(['details.entity', 'customer', 'details.clinic'])->whereBetween('created_at', [$fromdate, $todate])->where('status','=',$status)->orderBy('id', 'desc')->paginate(20);

	         }elseif($payment_status){
		   $orders=Order::with(['details.entity', 'customer', 'details.clinic'])->where('payment_status','=',$payment_status)->orderBy('id', 'desc')->paginate(20);

		     }elseif($status){
			$orders=Order::with(['details.entity', 'customer', 'details.clinic'])->where('status','=',$status)->orderBy('id', 'desc')->paginate(20);
             }elseif($search){
		           $orders=Order::with(['details.entity', 'customer', 'details.clinic'])->where('name','LIKE','%'.$search.'%')->orwhere('mobile','LIKE','%'.$search.'%')->orwhere('email','LIKE','%'.$search.'%')->orwhere('refid','LIKE','%'.$search.'%')->orderBy('id', 'desc')->paginate(20);
            }elseif($fromdate && $todate){	
			$orders=Order::with(['details.entity', 'customer', 'details.clinic'])->whereBetween('created_at', [$fromdate, $todate])->orderBy('id', 'desc')->paginate(20);
            }else{
        $orders=Order::with(['details.entity', 'customer', 'details.clinic'])->where('status', '!=', 'pending')->orderBy('id', 'desc')->paginate(20);
            }
            return view('admin.order.index', compact('orders'));
        }
        
        public function order_search(Request $request) {
	      $search=$request->input("search");
	      $payment_status=$request->input("payment_status");
	      $status=$request->input("status");
	     // var_dump($status); die;
	      $fromdate=$request->input("fromdate");
	      $todate1=$request->input("todate");
	      $todate = date('Y-m-d', strtotime($todate1. ' + 1 days'));
	      
	   if($payment_status && $search && $status && $fromdate && $todate){
		           $orders=Order::with(['details.entity', 'customer', 'details.clinic'])->whereBetween('created_at', [$fromdate, $todate])->where('payment_status','=',$payment_status)->where('status','=',$status)->where('name','LIKE','%'.$search.'%')->orwhere('mobile','LIKE','%'.$search.'%')->orwhere('email','LIKE','%'.$search.'%')->orwhere('refid','LIKE','%'.$search.'%')->orderBy('id', 'desc')->paginate(20);

		    }elseif($status && $fromdate && $todate){
			$orders=Order::with(['details.entity', 'customer', 'details.clinic'])->whereBetween('created_at', [$fromdate, $todate])->where('status','=',$status)->orderBy('id', 'desc')->paginate(20);

	         }elseif($payment_status){
		   $orders=Order::with(['details.entity', 'customer', 'details.clinic'])->where('payment_status','=',$payment_status)->orderBy('id', 'desc')->paginate(20);

		     }elseif($status){
			$orders=Order::with(['details.entity', 'customer', 'details.clinic'])->where('status','=',$status)->orderBy('id', 'desc')->paginate(20);
             }elseif($search){
		           $orders=Order::with(['details.entity', 'customer', 'details.clinic'])->where('name','LIKE','%'.$search.'%')->orwhere('mobile','LIKE','%'.$search.'%')->orwhere('email','LIKE','%'.$search.'%')->orwhere('refid','LIKE','%'.$search.'%')->orderBy('id', 'desc')->paginate(20);
            }elseif($fromdate && $todate){	
			$orders=Order::with(['details.entity', 'customer', 'details.clinic'])->whereBetween('created_at', [$fromdate, $todate])->orderBy('id', 'desc')->paginate(20);
            }else{
        $orders=Order::with(['details.entity', 'customer', 'details.clinic'])->where('status', '!=', 'pending')->orderBy('id', 'desc')->paginate(20);
            }
            return view('admin.order.product', compact('orders'));
        }

    public function details(Request $request, $id){
        $order=Order::with(['details.entity', 'customer'])->where('status', '!=', 'pending')->find($id);
        return view('admin.order.details', compact('order'));
    }

}
