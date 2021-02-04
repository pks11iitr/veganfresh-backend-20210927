<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaveLaterProduct extends Model
{
    protected $table='save_later_product';

    protected $fillable=['user_id','product_id','size_id'];

    protected $hidden =['created_at','updated_at','deleted_at'];
    public function product(){
        return $this->belongsTo('App\Models\Product', 'product_id');
    }
    public function sizeprice(){
        return $this->belongsTo('App\Models\Size', 'size_id')->where('isactive',1);
    }
}
