<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    protected $table='inventory';

    protected $fillable=['name','price','quantity','create_date', 'mrp','expiry','vendor','manufacturer','remarks'];

    public function getCreateDateAttribute($value){
        if(!empty($value)){
            return date('d-m-Y',strtotime($value));
        }
        return '';
    }


    public function getExpiryAttribute($value){
        if(!empty($value)){
            return date('d-m-Y',strtotime($value));
        }
        return '';
    }

}
