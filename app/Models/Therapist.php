<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use Illuminate\Support\Facades\Storage;

class Therapist extends Model
{
	protected $table='therapists';

    public function locations(){
        return $this->hasMany('App\Models\TherapistLocations', 'therapist_id')->orderBy('id', 'desc');
    }


    public function therapies(){
        return $this->belongsToMany('App\Models\Therapy', 'therapist_therapies', 'therapist_id', 'therapy_id')->withPivot('therapy_id', 'therapist_id', 'therapist_grade');
    }

}
