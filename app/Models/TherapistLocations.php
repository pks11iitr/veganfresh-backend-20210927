<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TherapistLocations extends Model
{
    protected $table='therapist_locations';

    protected $fillable=['lat','lang', 'therapist_id'];

}
