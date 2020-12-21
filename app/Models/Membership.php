<?php

namespace App\Models;

use App\Models\Traits\Active;
use Illuminate\Database\Eloquent\Model;

class Membership extends Model
{
    use Active;
    protected $table='memberships';

    protected $fillable=['name', 'price', 'validity','isactive','cashback'];



}
