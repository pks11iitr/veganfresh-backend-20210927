<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnProduct extends Model
{
    protected $table='return_product';

    protected $fillable=['order_id', 'entity_id','entity_type', 'size_id', 'image', 'name', 'price', 'cut_price', 'quantity'];

    public function order(){
        return $this->belongsTo('App\Models\Order', 'order_id');
    }
}
