<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use Illuminate\Support\Facades\Storage;

class TimeSlot extends Model
{
    protected $table='time_slot';
    protected $fillable=['from_time','to_time','isactive'];

    protected $hidden = ['created_at','deleted_at','updated_at'];

}
