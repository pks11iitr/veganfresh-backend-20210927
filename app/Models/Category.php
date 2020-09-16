<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use App\Models\Traits\Active;
use App\Models\Traits\DocumentUploadTrait;
use Illuminate\Support\Facades\Storage;

class Category extends Model
{
    use Active, DocumentUploadTrait;

    protected $table='categories';

    protected $fillable=['name','image','isactive'];

    protected $hidden = ['created_at','deleted_at','updated_at'];

    public function getImageAttribute($value){
        if($value)
            return Storage::url($value);
        return null;
    }
    public function subcategory(){
        return $this->hasMany('App\Models\SubCategory');
    }
}
