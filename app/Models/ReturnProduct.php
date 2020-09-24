<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnProduct extends Model
{
    protected $table='return_product';

    public function order(){
        return $this->belongsTo('App\Models\Order', 'order_id');
    }
}
