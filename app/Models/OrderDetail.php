<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $table='details';

    protected $fillable=[ 'order_id', 'product_id', 'size_id', 'quantity', 'image', 'price', 'cut_price', 'name'];

    public function entity(){
        return $this->morphTo();
    }

    public function clinic(){
        return $this->belongsTo('App\Models\Clinic', 'clinic_id');
    }
}
