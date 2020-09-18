<?php

namespace App\Models;

use App\Models\Traits\Active;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use Active;

    protected $table='coupons';

    protected $fillable=['code','discount_type','minimum_order', 'discount', 'isactive','usage_type','maximum_discount','expiry_date'];

}
