<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $table='details';

    protected $fillable=[ 'order_id', 'entity_id','entity_type', 'size_id', 'quantity', 'image', 'price', 'cut_price', 'name', 'status'];

    public function entity(){
        return $this->morphTo();
    }

    public function size(){
        return $this->belongsTo('App\Models\Size', 'size_id');
    }

    public static function removeOutOfStockItems($item){
        //foreach ($items as $item){
        if($item->entity->stock_type=='quantity'){
            if($item->entity->stock < $item->quantity){
                $item->delete();
                return true;
            }
        }else{
            if($item->size->stock < $item->quantity){
                $item->delete();
                return true;
            }
        }
        if($item->quantity < $item->size->min_qty || $item->quantity > $item->size->max_qty){
            $item->delete();
            return true;
        }

        return false;
        //}
    }
}
