<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\Order;
use App\Models\ReturnProduct;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReturnProductController extends Controller
{
    public function index(Request $request){
        $returnproducts=ReturnProduct::orderBy('id', 'desc')->paginate(10);

        return view('admin.retrunproduct.view',['returnproducts'=>$returnproducts]);

    }

}
