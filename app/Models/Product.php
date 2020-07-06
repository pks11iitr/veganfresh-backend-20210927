<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
	protected $table='products';
    protected $fillable=['name','description','company','price','cut_price','ratings','top_deal','best_seller','image','isactive'];

    protected $hidden = ['created_at','deleted_at','updated_at'];



    public function getImageAttribute($value){
        if($value)
            return Storage::url($value);
        return null;
    }

    public function category(){
        return $this->belongsToMany('App\Models\Category', 'product_categories', 'product_id', 'category_id');
    }

}
