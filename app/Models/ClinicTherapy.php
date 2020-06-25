<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use App\Models\Traits\Active;
use App\Models\Traits\DocumentUploadTrait;
use Illuminate\Support\Facades\Storage;

class ClinicTherapy extends Model
{
    protected $table='clinic_therapies';

	protected $fillable=['clinic_id','therapy_id','grade1_price','grade2_price','grade3_price','grade4_price','grade1_original_price','grade2_original_price','grade3_original_price','grade4_original_price','isactive'];

    protected $hidden = ['created_at','deleted_at','updated_at'];
    
    public function therapy(){
        return $this->belongsTo('App\Models\Therapy', 'therapy_id');
    }

}
