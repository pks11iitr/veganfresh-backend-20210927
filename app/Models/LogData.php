<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogData extends Model
{
    protected $table='logdata';

    protected $fillable=['data', 'type'];
}
