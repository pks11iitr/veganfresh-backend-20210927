<?php


namespace App\Models;

use App\Models\BaseModel as Model;
use App\Models\Traits\Active;
use App\Models\Traits\DocumentUploadTrait;
use Illuminate\Support\Facades\Storage;

class TherapiestWork extends Model
{
    protected $table = 'therapist_work';

    protected $fillable = ['therapist_id', 'home_booking_id', 'status'];

    protected $hidden = ['created_at', 'deleted_at', 'updated_at'];

    public function therapieswork(){
        return $this->belongsTo('App\Models\HomeBookingSlots', 'home_booking_id');
    }


}
