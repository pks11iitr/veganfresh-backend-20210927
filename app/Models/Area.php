<?php

namespace App\Models;

use App\Models\Traits\Active;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use Active;
    protected $table='area_list';

    protected $fillable=['name', 'isactive'];
}
