<?php

namespace App\Http\Controllers\Customer\Api;

use App\Models\BookingSlot;
use App\Models\Cart;
use App\Models\Clinic;
use App\Models\CustomerAddress;
use App\Models\DailyBookingsSlots;
use App\Models\HomeBookingSlots;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderStatus;
use App\Models\Product;
use App\Models\RescheduleRequest;
use App\Models\Therapy;
use App\Models\TimeSlot;
use App\Models\Wallet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    public function initiateOrder(Request $request){

        $user= auth()->guard('customerapi')->user();
        if(!$user)
            return [
                'status'=>'failed',
                'message'=>'Please login to continue'
            ];
        $cartitems=Cart::where('user_id', auth()->guard('customerapi')->user()->id)
            ->with(['product','sizeprice'])
            ->whereHas('product', function($product){
                $product->where('isactive', true);
            })->get();
        if(!$cartitems)
            return [
                'status'=>'failed',
                'message'=>'Cart is empty'
            ];

        $total_cost=0;
        foreach($cartitems as $item) {
            $total_cost=$total_cost+($item->sizeprice->price??0)*$item->quantity;

        }
        $refid=env('MACHINE_ID').time();
        $order=Order::create([
            'user_id'=>auth()->guard('customerapi')->user()->id??1,
            'refid'=>$refid,
            'status'=>'pending',
            'total_cost'=>$total_cost,
        ]);

        foreach($cartitems as $item){
            OrderDetail::create([
                'order_id'=>$order->id,
                'product_id'=>$item->product_id,
                'size_id'=>$item->size_id,
                'quantity'=>$item->quantity,
                'image'=>$item->zizeprice->image,
                'price'=>$item->sizeprice->price,
                'cut_price'=>$item->sizeprice->cut_price,
            ]);
        }

        return [
            'status'=>'success',
            'data'=>[
                'order_id'=>$order->id
            ]
        ];

    }


    public function selectAddress(Request $request, $id){

        $request->validate([
            'address_id'=>'required|integer'
        ]);

        $user= auth()->guard('customerapi')->user();
        if(!$user)
            return [
                'status'=>'failed',
                'message'=>'Please login to continue'
            ];

        $address=CustomerAddress::where('user_id', $user->id)
            ->find($request->address_id);
        if(!$address)
            return [
                'status'=>'failed',
                'message'=>'No address Found'
            ];

        $order=Order::where('user_id', $user->id)
            ->where('status', 'pending')
            ->find($id);

        if(!$order)
            return [
                'status'=>'failed',
                'message'=>'No Order Found'
            ];

        $order->address_id=$address->id;

        $order->save();

        return [
            'status'=>'success',
            'message'=>'Address Has Been Updated'
        ];


    }

}
