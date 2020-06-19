<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use Illuminate\Support\Facades\Storage;

class Category extends Model
{
    protected $table='categories';

    public function getImageAttribute($value){
        if($value)
            return Storage::url($value);
        return null;
    }
}
