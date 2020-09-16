<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class HomeSection extends Model
{
    protected $table='home_sections';

    protected $fillable=['sequence_no','name','image','type','isactive'];

    protected $hidden = ['created_at','deleted_at','updated_at'];

    public function entity(){
        return $this->morphTo();
    }

    public function getImageAttribute($value){
        if($value)
            return Storage::url($value);
        return null;
    }
}
