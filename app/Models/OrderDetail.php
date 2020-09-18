<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $table='details';

    protected $fillable=[ 'order_id', 'entity_id','entity_type', 'size_id', 'quantity', 'image', 'price', 'cut_price', 'name'];

    public function entity(){
        return $this->morphTo();
    }

    public function clinic(){
        return $this->belongsTo('App\Models\Size', 'size_id');
    }
}
