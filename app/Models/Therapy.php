<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use App\Models\Traits\Active;
use App\Models\Traits\DocumentUploadTrait;
use Illuminate\Support\Facades\Storage;

class Therapy extends Model
{
    protected $table='therapies';


    public function getImageAttribute($value){
        if($value)
            return Storage::url($value);
        return '';
    }


}
