<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $table='area_list';

    protected $fillable=['name', 'isactive'];
}
