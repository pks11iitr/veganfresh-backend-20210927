<?php

namespace App\Models;

use App\Models\Traits\Active;
use App\Models\Traits\DocumentUploadTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Video extends Model
{
    use Active, DocumentUploadTrait;

    protected $table='videos';

    protected $fillable=['url', 'image', 'isactive'];


    public function getImageAttribute($value){
        return Storage::url($value);
    }

}
