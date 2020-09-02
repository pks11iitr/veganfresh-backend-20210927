<?php


namespace App\Models;

use App\Models\BaseModel as Model;
use App\Models\Traits\Active;
use App\Models\Traits\DocumentUploadTrait;
use Illuminate\Support\Facades\Storage;

class UpdateAvalibility extends Model
{
    protected $table = 'update_availability';

    protected $fillable = ['is_available', 'from_date', 'to_date', 'therapiest_id'];

    protected $hidden = ['created_at', 'deleted_at', 'updated_at'];


}
