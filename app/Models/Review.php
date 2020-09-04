<?php

namespace App\Models;

use App\Models\Traits\Active;
use App\Models\BaseModel as Model;
use Illuminate\Support\Facades\Storage;

class Review extends Model
{
    use Active;

    protected $table='reviews';

    protected $fillable=['user_id','comment','description', 'rating', 'product_id', 'image','image1'];

    protected $hidden=['id', 'updated_at','deleted_at', 'user_id', 'isactive'];

    public function customer(){
        return $this->belongsTo('App\Models\Customer', 'user_id');
    }
    public function getImageAttribute($value){
        if($value)
            return Storage::url($value);
        return null;
    }
    public function getImage1Attribute($value){
        if($value)
            return Storage::url($value);
        return null;
    }

}
