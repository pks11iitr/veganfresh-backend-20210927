<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    protected $hidden = ['created_at','deleted_at','updated_at'];
}
