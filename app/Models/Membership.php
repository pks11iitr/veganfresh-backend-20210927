<?php

namespace App\Models;

use App\Models\Traits\Active;
use Illuminate\Database\Eloquent\Model;

class Membership extends Model
{
    use Active;
    protected $table='memberships';

    protected $fillable=['name', 'price', 'validity','isactive','cashback'];


    public static function creditMembershipCashback($order, $user){

        if($user->membership_expiry>=date('Y-m-d')){

            $membership=Membership::active()->find($user->active_membership);
            if(!$membership){
                return ;
            }

            $cashback=round(($order->total_cost-$order->points_used-$order->coupon_discount)*$membership->cashback/100, 2);

            if($cashback>0)
                Wallet::updatewallet($user->id, 'Cashback for Order Id: '.$order->refid, 'Credit', $cashback, 'POINT', $order->id);

        }

    }



}
