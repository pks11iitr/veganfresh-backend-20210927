<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use App\Models\Traits\Active;
use App\Models\Traits\DocumentUploadTrait;

class Product extends Model
{
    use Active, DocumentUploadTrait;
}
