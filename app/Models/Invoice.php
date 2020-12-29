<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $table ='invoice';

    protected $fillable=['prefix','sequence','current_sequence', 'pan_gst', 'address'];
}
