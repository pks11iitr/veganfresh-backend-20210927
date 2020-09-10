<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use Illuminate\Support\Facades\Storage;

class Banner extends Model
{
    protected $table='banners';
    protected $fillable=['image','isactive','entity_type','parent_category','entity_id'];

    protected $hidden = ['created_at','deleted_at','updated_at'];

    public function entity(){
        return $this->morphTo();
    }

    public function getImageAttribute($value){
        if($value)
            return Storage::url($value);
        return null;
    }

    public function category(){
        return $this->belongsTo('App\Models\Category', 'entity_id');
    }

    public function subcategory(){
        return $this->belongsTo('App\Models\SubCategory', 'entity_id');
    }

    public function offercategory(){
        return $this->belongsTo('App\Models\OfferCategory', 'entity_id');
    }
}
