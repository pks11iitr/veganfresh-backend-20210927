<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComplaintMessage extends Model
{
    protected $table='complaint_messages';

    public function complaint(){
        return $this->belongsTo('App\Models\Complaint', 'complaint_id');
    }
}
