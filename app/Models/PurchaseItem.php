<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    protected $table='inventory';

    protected $fillable=['name','price','quantity','create_date', 'mrp','expiry','vendor','manufacturer','remarks'];

}
