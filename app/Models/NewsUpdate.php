<?php

namespace App\Models;
use App\Models\BaseModel as Model;
use App\Models\Traits\Active;
use App\Models\Traits\DocumentUploadTrait;
use Illuminate\Support\Facades\Storage;

class NewsUpdate extends Model
{
    use Active;
    protected $table='news_update';

    protected $fillable = ['image', 'description', 'isactive'];
    
    public function getImageAttribute($value){
        if($value)
            return Storage::url($value);
        return '';
    }
}
