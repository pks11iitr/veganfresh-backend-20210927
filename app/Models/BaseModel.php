<?php

namespace App\Models;

use App\Models\Traits\Active;
use App\Models\Traits\DocumentUploadTrait;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{

    use Active, DocumentUploadTrait;

    protected $hidden = ['created_at','deleted_at','updated_at'];

}
