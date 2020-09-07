<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Size extends Model
{
    protected $table='product_prices';

    protected $fillable=['size', 'price','cut_price','product_id', 'isactive'];

    protected $hidden =['created_at','updated_at','deleted_at'];
    protected $appends=['discount'];

    public function getDiscountAttribute($value){
        return intval((($this->cut_price-$this->price)/$this->cut_price)*100);
    }
}
