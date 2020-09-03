<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
	protected $table='products';
    protected $fillable=['name','description','company','ratings','image','isactive'];

    protected $hidden = ['created_at','deleted_at','updated_at'];



    public function getImageAttribute($value){
        if($value)
            return Storage::url($value);
        return null;
    }
		public function subcategory(){
        return $this->belongsToMany('App\Models\SubCategory', 'product_category', 'product_id', 'sub_cat_id');
    }
		public function category(){
        return $this->belongsToMany('App\Models\Category', 'product_category', 'product_id', 'category_id');
    }
		public function sizeprice(){
        return $this->hasMany('App\Models\Size', 'product_id');
    }

		//
    // public function category(){
    //     return $this->belongsToMany('App\Models\Category', 'product_categories', 'product_id', 'category_id');
    // }

}
