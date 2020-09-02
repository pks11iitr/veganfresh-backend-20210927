<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FavoriteProduct extends Model
{
    protected $table='favorite_products';

    protected $fillable=['user_id','product_id'];

    protected $hidden =['created_at','updated_at','deleted_at'];
}
