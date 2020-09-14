<?php

namespace App\Models;

use App\Models\Traits\Active;
use App\Models\Traits\ReviewTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Size extends Model
{
    use ReviewTrait;use Active;
    protected $table='product_prices';

    protected $fillable=['size', 'price','cut_price','product_id', 'isactive','id'];

    protected $hidden =['created_at','updated_at','deleted_at','isactive'];
    protected $appends=['discount'];

    public function getDiscountAttribute($value){
        return empty($this->cut_price)?0:intval((($this->cut_price-$this->price)/$this->cut_price)*100);
    }
    public function getImageAttribute($value){
        if($value)
            return Storage::url($value);
        return null;
    }
//product details multiple image with size
    public function images(){
        return $this->hasMany('App\Models\ProductImage','size_id');
    }

    public function product(){
        return $this->belongsTo('App\Models\Product', 'product_id');
    }

}
