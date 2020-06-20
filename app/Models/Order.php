<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table='orders';

    protected $fillable=[ 'refid', 'total_cost', 'status', 'payment_mode', 'order_details_completed'];

}
