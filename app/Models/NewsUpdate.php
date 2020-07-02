<?php

namespace App\Models;

use App\Models\Traits\Active;
use Illuminate\Database\Eloquent\Model;

class NewsUpdate extends Model
{
    use Active;
    protected $table='news_update';

    protected $fillable = ['image', 'description', 'isactive'];
}
