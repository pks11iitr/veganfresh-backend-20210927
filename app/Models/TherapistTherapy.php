<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TherapistTherapy extends Model
{
    protected $table='therapist_therapies';

    protected $fillable=['therapist_id', 'therapy_id', 'therapist_grade', 'isactive'];


}
