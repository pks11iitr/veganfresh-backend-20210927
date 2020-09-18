<?php

namespace App\Models;

use App\Models\Traits\Active;
use App\Models\Traits\DocumentUploadTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class HomeSectionEntity extends Model
{
    use Active, DocumentUploadTrait;

    protected $table='home_section_entities';

    protected $fillable=['home_section_id','entity_type','entity_id','name','image'];

    protected $hidden = ['created_at','deleted_at','updated_at'];

    public function entity(){
        $this->morphTo();
    }

    public function getImageAttribute($value){
        if($value)
            return Storage::url($value);
        return null;
    }

    public function product(){
        return $this->belongsTo('App\Models\Product', 'entity_id');
    }

    public function homesection(){
        return $this->belongsTo('App\Models\HomeSection', 'home_section_id');
    }

    public function subcategory(){
        return $this->belongsTo('App\Models\SubCategory', 'entity_id');
    }


}
