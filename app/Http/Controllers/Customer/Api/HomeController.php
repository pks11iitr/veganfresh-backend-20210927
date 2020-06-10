<?php

namespace App\Http\Controllers\Customer\Api;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function home(Request $request){


        $products=Product::active()->where('show_on_home', true)->get();

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

        $videos=[

        ];

        return [
            'status'=>'success',
            'data'=>compact('services','products','videos')
        ];
    }
}
