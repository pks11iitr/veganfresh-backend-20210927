<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $table='details';

    protected $fillable=[ 'order_id', 'entity_id', 'entity_type', 'clinic_id', 'description', 'cost', 'quantity', 'grade'];

    public function entity(){
        return $this->morphTo();
    }

    public function clinic(){
        return $this->belongsTo('App\Models\Clinic', 'clinic_id');
    }
}
