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

    protected $fillable=[ 'refid', 'total_cost', 'status', 'payment_status', 'payment_mode', 'order_details_completed', 'booking_date', 'booking_time', 'user_id', 'name', 'email', 'mobile', 'address', 'lat', 'lang', 'is_instant', 'use_wallet', 'use_points', 'balance_used', 'points_used','schedule_type','order_place_state','coupon_applied', 'coupon_discount', 'delivery_charge', 'delivery_date', 'delivery_slot', 'delivered_at', 'cashback_given','store_id','is_express_delivery','return_reason', 'payment_collect_mode'];

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
        $discount=$this->getCouponDiscount($coupon);
        $this->coupon_applied=$coupon->code;
        $this->coupon_discount=$discount;
        $this->save();

    }

    public function getCouponDiscount($coupon){
        $eligible_amount=$this->getDiscountEligibleAmount($coupon);
        $discount=$coupon->getCouponDiscount($eligible_amount);
        return $discount;
    }

    public function getDiscountEligibleAmount($coupon){
        $amount=0;
        $coupon_cat=$coupon->categories->map(function($element){
            return $element->id;
        });
        $coupon_cat=$coupon_cat->toArray();
        foreach($this->details as $detail){
            if(count($coupon_cat)){
                $product_cat=$detail->entity->subcategory->map(function($element){
                    return $element->id;
                });
                $product_cat=$product_cat->toArray();
                if(count(array_intersect($product_cat,$coupon_cat))){
                    $amount=$amount+$detail->price*$detail->quantity;
                }
            }else{
                $amount=$amount+$detail->price*$detail->quantity;
            }
        }
        return $amount;
    }

    public function getMembershipEligibleDiscount($membership){
        $amount=0;
        $membership_cat=$membership->categories->map(function($element){
            return $element->id;
        });
        $membership_cat=$membership_cat->toArray();
        foreach($this->details as $detail){
            if(count($membership_cat)){
                $product_cat=$detail->entity->subcategory->map(function($element){
                    return $element->id;
                });
                $product_cat=$product_cat->toArray();
                if(count(array_intersect($product_cat,$membership_cat))){
                    $amount=$amount+$detail->price*$detail->quantity;
                }
            }else{
                $amount=$amount+$detail->price*$detail->quantity;
            }
        }
        return $amount;
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

    public static function setInvoiceNumber($order){

        if(empty($order->invoice_number)){
            Invoice::where('id', 1)->update(['current_sequence'=>DB::raw('current_sequence+1')]);
            $invoice_sequence=Invoice::find(1);
            $current=$invoice_sequence->current_sequence;
            $current=$current+1;
            $order->invoice_number=$invoice_sequence->prefix.$current;
            $order->save();
        }


    }


}
