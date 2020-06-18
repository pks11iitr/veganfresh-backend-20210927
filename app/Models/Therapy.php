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

//    public function getGrade1OriginalPriceAttribute($value){
//        if($value==null)
//            return '';
//        return $value;
//    }
//    public function getGrade2OriginalPriceAttribute($value){
//        if($value==null)
//            return '';
//        return $value;
//    }
//    public function getGrade3OriginalPriceAttribute($value){
//        if($value==null)
//            return '';
//        return $value;
//    }
//    public function getGrade4OriginalPriceAttribute($value){
//        if($value==null)
//            return '';
//        return $value;
//    }
//    public function getGrade1PriceAttribute($value){
//        if($value==null)
//            return '';
//        return $value;
//    }
//    public function getGrade2PriceAttribute($value){
//        if($value==null)
//            return '';
//        return $value;
//    }
//    public function getGrade3PriceAttribute($value){
//        if($value==null)
//            return '';
//        return $value;
//    }
//    public function getGrade4PriceAttribute($value){
//        if($value==null)
//            return '';
//        return $value;
//    }


}
