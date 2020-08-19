<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use App\Models\Traits\DocumentUploadTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Therapist extends Authenticatable implements JWTSubject
{
    use DocumentUploadTrait;

	protected $table='therapists';

	protected $fillable=['name','email','mobile','password', 'image'];

    public function locations(){
        return $this->hasMany('App\Models\TherapistLocations', 'therapist_id')->orderBy('id', 'desc');
    }


    public function therapies(){
        return $this->belongsToMany('App\Models\Therapy', 'therapist_therapies', 'therapist_id', 'therapy_id')->withPivot('therapy_id', 'therapist_id', 'therapist_grade', 'id', 'isactive');
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function getImageAttribute($value){
        if($value)
            return Storage::url($value);
        return null;
    }

}
