<?php

namespace App\Models;

use App\Models\Traits\DocumentUploadTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Customer extends Authenticatable implements JWTSubject
{
    use DocumentUploadTrait;

    protected $table='customers';

    protected $fillable = [
        'name','last_name', 'email', 'mobile', 'password', 'image', 'dob','address','city', 'state','status','pincode','notification_token', 'area_id','active_membership','membership_expiry'
    ];

    protected $hidden = [
        'password','created_at','deleted_at','updated_at','email','mobile'
    ];

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


    public function favouriteProducts(){
        return $this->belongsToMany('App\Models\Product', 'favorite_products', 'user_id', 'product_id');
    }


    public function isMembershipActive(){
        if($this->active_membership && $this->membership_expiry > date('Y-m-d')){
            return true;
        }
        return false;
    }

    public function membership(){
        return $this->belongsTo('App\Models\Membership', 'active_membership');
    }

    public function area(){
        return $this->belongsTo('App\Models\Area', 'area_id');
    }

}
