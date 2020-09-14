<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $table= 'cart';

    protected $fillable = ['user_id', 'product_id', 'quantity','size_id'];


    public function product(){
        return $this->belongsTo('App\Models\Product', 'product_id');
    }
    public function sizeprice(){
        return $this->belongsTo('App\Models\Size', 'size_id');
    }
    public function images(){
        return $this->belongsTo('App\Models\ProductImage', 'size_id');
    }

    public static function getUserCart($user){
        if(!$user)
            return [];
        $cart=[];
        $items=Cart::where('user_id', $user->id)->get();
        foreach ($items as $item)
            $cart[$item->size_id]=$item->quantity;
        return $cart;
    }

}
