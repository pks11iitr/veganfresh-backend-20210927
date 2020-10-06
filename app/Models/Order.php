<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Order extends Model
{

    /*
     * List of order status:
     * Products: pending, confirmed, cancelled, processing, return-requested, 'returned', 'completed'
     * Therapies: pending, confirmed, cancelled, processing, completed
     */
    protected $table='orders';

    protected $fillable=[ 'refid', 'total_cost', 'status', 'payment_status', 'payment_mode', 'order_details_completed', 'booking_date', 'booking_time', 'user_id', 'name', 'email', 'mobile', 'address', 'lat', 'lang', 'is_instant', 'use_wallet', 'use_points', 'balance_used', 'points_used','schedule_type','order_place_state','coupon_applied', 'coupon_discount', 'delivery_charge', 'delivery_date', 'delivery_slot', 'delivered_at', 'cashback_given','store_id'];

    public function details(){
        return $this->hasMany('App\Models\OrderDetail', 'order_id');
    }

    public function customer(){
        return $this->belongsTo('App\Models\Customer', 'user_id');
    }

    public function storename(){
        return $this->belongsTo('App\Models\User', 'store_id');
    }

    public function rider(){
        return $this->belongsTo('App\Models\Rider', 'rider_id');
    }


    public function deliveryaddress(){
        return $this->belongsTo('App\Models\CustomerAddress', 'address_id');
    }

    public function reviews(){
        return $this->hasMany('App\Models\Review', 'order_id');
    }

    public static function getTotal(Order $order){
        $cost=0;

        foreach($order->details as $d){
            $cost = $cost+$d->sizeprice->price;
        }

        return $cost;


    }

    public function applyCoupon($coupon){

        $discount=$coupon->getCouponDiscount($this->total_cost);
        $this->coupon_applied=$coupon->code;
        $this->coupon_discount=$discount;
        $this->save();

    }

    public function changeDetailsStatus($status, $id=null){
        if($id==null){

            $this->details()->update(['status'=>$status]);

        }else{

            $this->details()->where('details.id', $id)->update(['status'=>$status]);

        }
    }

    public function timeslot(){
        return $this->belongsTo('App\Models\TimeSlot', 'delivery_slot');
    }


    public function returned(){
        return $this->hasMany('App\Models\ReturnProduct', 'order_id');
    }


    public static function deductInventory($order){

        foreach($order->details as $detail){

            if($detail->entity->stock_type=='quantity'){

                Product::where('id', $detail->entity_id)
                    ->update(['stock'=>DB::raw('stock-'.($detail->quantity*$detail->size->consumed_units))]);

            }else{
                Size::where('id', $detail->size_id)
                    ->update(['stock'=>DB::raw('stock-'.($detail->quantity*$detail->size->consumed_units))]);
            }
        }
    }


    public static function increaseInventory($order){

        foreach($order->details as $detail){

                self::increaseItemCount($detail, ($detail->quantity*$detail->size->consumed_units));

        }
    }

    public static function increaseItemCount($detail, $i){

        if($detail->entity->stock_type=='quantity'){

            Product::where('id', $detail->entity_id)
                ->update(['stock'=>DB::raw('stock+'.$i)]);

        }else{
            Size::where('id', $detail->size_id)
                ->update(['stock'=>DB::raw('stock+'.$i)]);
        }

    }



}
