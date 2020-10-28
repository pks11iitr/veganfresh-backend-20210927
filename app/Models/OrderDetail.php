<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $table='details';

    protected $fillable=[ 'order_id', 'entity_id','entity_type', 'size_id', 'quantity', 'image', 'price', 'cut_price', 'name', 'status'];

    protected $appends=['cost', 'price_str', 'cut_price_str'];

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

    public function order(){
        return $this->belongsTo('App\Models\Order', 'order_id');
    }


    public function getPriceStrAttribute($value){

        if(is_decimal($this->price)){
            return $this->price.'';
        }else{
            return intval($this->price).'';
        }

    }

    public function getCutPriceStrAttribute($value){

        if(is_decimal($this->cut_price)){
            return $this->cut_price.'';
        }else{
            return intval($this->cut_price).'';
        }

    }

    public function getCostStrAttribute($value){

        if(is_decimal($this->cost)){
            return $this->cost.'';
        }else{
            return intval($this->cost).'';
        }

    }


    public function getPriceAttribute($value){

        if(is_decimal($value)){
            return $value.'';
        }else{
            return intval($value).'';
        }

    }

    public function getCutPriceAttribute($value){

        if(is_decimal($value)){
            return $value.'';
        }else{
            return intval($value).'';
        }

    }
}
