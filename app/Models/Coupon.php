<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $table='coupons';

    protected $fillable=['code', 'discount_type', 'discount', 'isactive', 'is_used', 'minimum_order', 'maximum_discount', 'expiry_date', 'usage_type'];


    public function getCouponDiscount($amount){

        if($this->type=='fixed'){
            $discount=$this->discount;
        }else{
            $discount=floor($amount*$this->discount/100);
        }

        if($amount<$this->minimum_order){
            return 0;
        }

        if($this->maximum_discount && $amount>$this->maximum_discount)
        {
            return $this->maximum_discount;
        }
        return $discount;
    }

    public function getUserEligibility($user){

        switch($this->usage_type){
            case 'single-singleuser':
                $order=Order::where('coupon_applied', $this->code)->first();
                if($order)
                    return false;
                break;
            case 'single-multipleuser':break;
                $order=Order::where('coupon_applied', $this->code)
                    ->where('user_id', $user->id)
                    ->first();
                if($order)
                    return false;
                break;
            case 'multiple-multipleuser':
                break;

        }

        return true;

    }



}