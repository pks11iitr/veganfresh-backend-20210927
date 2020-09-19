<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use App\Models\Traits\Active;
use App\Models\Traits\DocumentUploadTrait;
use App\Models\Traits\ReviewTrait;
use Illuminate\Support\Facades\Storage;

class ProductImage extends Model
{
    use ReviewTrait, Active, DocumentUploadTrait;
    protected $table='product_images';
    protected $fillable=['product_id','size_id','image','entity_id','entity_type'];

    protected $hidden = ['created_at','deleted_at','updated_at'];

    public function getImageAttribute($value){
        if($value)
            return Storage::url($value);
        return null;
    }
    public function entity(){
        $this->morphTo();
    }

}
