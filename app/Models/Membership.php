<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Membership extends Model
{
    protected $table='memberships';

    protected $fillable=['name', 'price', 'validity'];
}
