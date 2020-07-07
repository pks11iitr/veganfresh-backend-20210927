<?php

namespace App\Http\Controllers\Customer\Api;

use App\Models\Banner;
use App\Models\Configuration;
use App\Models\Product;
use App\Models\Video;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function home(Request $request){

        $banners=Banner::active()->get();
        $products=Product::active()->where('top_deal', true)->get();
        $videos=Video::active()->get();
        $services=[
            [
            'name'=>'Therapy at clinics',
            'url'=>route('clinics.list')
            ],
            [
                'name'=>'Therapy at home',
                'url'=>route('therapies.list')
            ],
        ];
        $channel_url=Configuration::where('param', 'channel_url')->first();
        $channel_url=$channel_url->value;
        return [
            'status'=>'success',
            'data'=>compact('services','products','videos', 'banners', 'channel_url')
        ];
    }
}
