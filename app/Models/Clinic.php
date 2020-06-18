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

    public function therapies(){
        return $this->belongsToMany('App\Models\Therapy', 'clinic_therapies', 'clinic_id', 'therapy_id');
    }


    public function getGrade1OriginalPriceAttribute($value){

    }
}
