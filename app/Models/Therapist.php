<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use Illuminate\Support\Facades\Storage;

class Therapist extends Model
{ 
	protected $table='therapies';
	protected $fillable=['name','description','grade1_price','grade2_price','grade3_price','grade4_price','image','isactive'];

    protected $hidden = ['created_at','deleted_at','updated_at'];
    public function locations(){
        return $this->hasMany('App\Models\TherapistLocations', 'therapist_id')->orderBy('id', 'desc');
    }


    public function therapies(){
        return $this->belongsToMany('App\Models\Therapy', 'therapist_therapies', 'therapist_id', 'therapy_id');
    }
     public function getImageAttribute($value){
        if($value)
            return Storage::url($value);
        return null;
    }
}
