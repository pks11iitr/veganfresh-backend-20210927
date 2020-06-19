<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{

    protected $table='products';


    public function getImageAttribute($value){
        if($value)
            return Storage::url($value);
        return null;
    }

}
