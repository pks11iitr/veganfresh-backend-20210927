<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Document extends Model
{
    protected $table='product_images';

    protected $fillable=['product_id','size_id', 'image',];

    protected $hidden = ['created_at','deleted_at','updated_at'];

    public function entity(){
        $this->morphTo();
    }

    public function getFilePathAttribute($value){
        if($value)
            return Storage::url($value);
        return '';
    }

}
