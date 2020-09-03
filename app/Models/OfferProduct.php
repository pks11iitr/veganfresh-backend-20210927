<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use Illuminate\Support\Facades\Storage;

class OfferProduct extends Model
{
    protected $table='offer_product';
    protected $fillable=['offer_cat_id','product_id'];

    protected $hidden = ['created_at','deleted_at','updated_at'];

    public function getImageAttribute($value)
    {
        if ($value)
            return Storage::url($value);
        return null;
    }
}
