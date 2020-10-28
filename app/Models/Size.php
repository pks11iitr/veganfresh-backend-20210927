<?php

namespace App\Models;

use App\Models\Traits\Active;
use App\Models\Traits\DocumentUploadTrait;
use App\Models\Traits\ReviewTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Size extends Model
{
    use ReviewTrait, Active,DocumentUploadTrait;
    protected $table='product_prices';

    protected $fillable=['size', 'price','cut_price','product_id', 'isactive','min_qty','max_qty','stock','image','consumed_units'];

    protected $hidden =['created_at','updated_at','deleted_at','isactive'];
    protected $appends=['discount','price_str', 'cut_price_str'];

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


    public static function getStockStatus($size, $product){

        if($product->stock_type=='packet'){

            if($size->stock > 0)
                return 1;
            else
                return 0;

        }else if($product->stock_type=='quantity'){

            if($product->stock > 0 && $product->stock >= $size->consumed_units )
                return 1;
            else
                return 0;
        }

        return 0;

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
