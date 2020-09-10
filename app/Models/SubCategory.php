<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use Illuminate\Support\Facades\Storage;

class SubCategory extends Model
{
    protected $table='sub_category';

    protected $fillable=['name','category_id','isactive'];

    protected $hidden = ['created_at','deleted_at','updated_at'];

    public function entity_type(){
        return $this->morphTo();
    }


}
