<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnRequest extends Model
{
    protected $table = 'return_requests';

    protected $fillable = ['order_id', 'details_id', 'quantity'];

}
