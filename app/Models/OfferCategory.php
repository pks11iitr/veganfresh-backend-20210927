<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use Illuminate\Support\Facades\Storage;

class OfferCategory extends Model
{
    protected $table='offer_category';
    protected $fillable=['name','isactive','image'];

    protected $hidden = ['created_at','deleted_at','updated_at'];

    public function getImageAttribute($value){
        if($value)
            return Storage::url($value);
        return null;
    }

}
