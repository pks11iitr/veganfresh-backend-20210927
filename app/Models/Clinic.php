<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use Illuminate\Support\Facades\Storage;

class Clinic extends Model
{
    protected $table = 'clinics';

    public function getImageAttribute($value){
        if($value)
            return Storage::url($value);
        return null;
    }
}
