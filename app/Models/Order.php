<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{

    /*
     * List of order status:
     * Products: pending, confirmed, cancelled, processing, return-requested, 'returned', 'completed'
     * Therapies: pending, confirmed, cancelled, processing, completed
     */
    protected $table='orders';

    protected $fillable=[ 'refid', 'total_cost', 'status', 'payment_status', 'payment_mode', 'order_details_completed', 'booking_date', 'booking_time', 'user_id', 'name', 'email', 'mobile', 'address', 'lat', 'lang', 'is_instant', 'use_wallet', 'use_points', 'balance_used', 'points_used','schedule_type','order_place_state','coupon_applied', 'coupon_discount'];

    public function details(){
        return $this->hasMany('App\Models\OrderDetail', 'order_id');
    }

    public function customer(){
        return $this->belongsTo('App\Models\Customer', 'user_id');
    }


    public function deliveryaddress(){
        return $this->belongsTo('App\Models\CustomerAddress', 'address_id');
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

            $this->details()->update(['status', $status]);

        }else{

            $this->details()->where('details.id', $id)->update(['status', $status]);

        }
    }




}
