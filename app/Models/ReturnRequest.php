<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnRequest extends Model
{
    protected $table = 'return_requests';

    protected $fillable = ['order_id', 'details_id', 'quantity', 'return_reason', 'remark', 'product_id', 'size_id', 'price', 'rider_status','status', 'rider_id', 'user_id', 'store_id'];

    public function order(){
        return $this->belongsTo('App\Models\Order', 'order_id');
    }

    public function details(){
        return $this->belongsTo('App\Models\OrderDetail', 'details_id');
    }

    public function size(){
        return $this->belongsTo('App\Models\Size', 'size_id');
    }

    public function product(){
        return $this->belongsTo('App\Models\Product', 'product_id');
    }

    public function store(){
        return $this->belongsTo('App\Models\User', 'store_id');
    }

}
