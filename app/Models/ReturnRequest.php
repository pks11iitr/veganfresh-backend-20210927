<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnRequest extends Model
{
    protected $table = 'return_requests';

    protected $fillable = ['order_id', 'details_id', 'quantity', 'return_reason'];

    public function order(){
        return $this->belongsTo('App\Models\Order', 'order_id');
    }

    public function details(){
        return $this->belongsTo('App\Models\OrderDetail', 'details_id');
    }

    public function size(){
        return $this->belongsToMany('App\Models\Size', 'details', 'order_id', 'size_id');
    }

}
