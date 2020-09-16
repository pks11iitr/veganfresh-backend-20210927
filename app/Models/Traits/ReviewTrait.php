<?php

namespace App\Models\Traits;
use DB;

trait ReviewTrait {

    public function reviews(){
        return $this->hasMany('App\Models\Review', 'product_id');
    }

    public function reviews_count(){
        return $this->reviews()
            ->selectRaw('avg(rating) as rating, count(*) as review, product_id')
            ->where('reviews.isactive', true)
            ->groupBy('product_id');
    }

    public function avg_reviews(){
        return $this->reviews()
            ->selectRaw('Format(avg(rating), 1) as rating')
            ->where('reviews.isactive', true);
    }

    public function commentscount(){
        return $this->reviews()->where('reviews.description', '!=', null)
            ->selectRaw('entity_id, count(*) as comments')
            ->where('isactive', true)
            ->groupBy('entity_id');;
    }

    public function comments(){
        return $this->reviews()->where('reviews.description', '!=', null);
    }

    public function customer(){
        return $this->belongsTo('App\Models\Customer', 'user_id');
    }







}
