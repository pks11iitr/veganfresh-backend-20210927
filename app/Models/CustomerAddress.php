<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use Illuminate\Support\Facades\Storage;

class CustomerAddress extends Model
{
    protected $table='customer_address';
    protected $fillable=['user_id','first_name','last_name','mobile_no','email','house_no','appertment_name','street','landmark','area','city','pincode','address_type','other_text'];

    protected $hidden = ['created_at','deleted_at','updated_at'];

}
