<?php


namespace App\Models;

use App\Models\BaseModel as Model;
use App\Models\Traits\Active;
use App\Models\Traits\DocumentUploadTrait;
use Illuminate\Support\Facades\Storage;

class Treatment extends Model
{
    protected $table = 'treatment';

    protected $fillable = ['name', 'isactive'];

    protected $hidden = ['created_at', 'deleted_at', 'updated_at'];

}

