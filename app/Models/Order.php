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

    protected $fillable=[ 'refid', 'total_cost', 'status', 'payment_status', 'payment_mode', 'order_details_completed', 'booking_date', 'booking_time', 'user_id', 'name', 'email', 'mobile', 'address', 'lat', 'lang', 'is_instant', 'use_wallet', 'use_points', 'balance_used', 'points_used','schedule_type','order_place_state'];

    public function details(){
        return $this->hasMany('App\Models\OrderDetail', 'order_id');
    }

    public function customer(){
        return $this->belongsTo('App\Models\Customer', 'user_id');
    }

    public function getOrderDescription(){

    }


}
