<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeSectionEntity extends Model
{
    protected $table='home_section_entities';

    protected $fillable=['home_section_id','entity_type','entity_id','name','image'];

    protected $hidden = ['created_at','deleted_at','updated_at'];
}
