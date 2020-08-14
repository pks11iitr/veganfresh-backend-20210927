<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payments extends Model
{
    protected $table='payments';

    protected $fillable=['order_id', 'razorpay_order_id', 'razorpay_order_id_response', 'razorpay_payment_id', 'razorpay_payment_id_response', 'amount', 'confirmed'];

    public function order(){
        return $this->belongsTo('App\Models\Order', 'order_id');
    }

}
