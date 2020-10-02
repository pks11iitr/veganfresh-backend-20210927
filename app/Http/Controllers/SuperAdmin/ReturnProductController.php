<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\Order;
use App\Models\ReturnProduct;
use App\Models\Rider;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReturnProductController extends Controller
{
    public function index(Request $request)
    {

        $returnproducts = ReturnProduct::where(function ($returnproducts) use ($request) {
            // $returnproducts=ReturnProduct::orderBy('id', 'desc')->paginate(10);
            $returnproducts->where('name', 'like', "%" . $request->search . "%")
                ->orWhere('ref_id', 'like', "%" . $request->search . "%");
        });

            if($request->fromdate)
                $returnproducts=$returnproducts->where('created_at', '>=', $request->fromdate.'00:00:00');

            if($request->todate)
                $returnproducts=$returnproducts->where('created_at', '<=', $request->todate.'23:59:50');

            if($request->store_id)
                $returnproducts=$returnproducts->where('store_id', $request->store_id);
            $returnproducts=$returnproducts->orderBy('id', 'desc')->paginate(10);
            $stores=User::where('id','>', 1)->get();
            $riders=Rider::get();

            return view('admin.retrunproduct.view', ['returnproducts' => $returnproducts,'stores'=>$stores, 'riders'=>$riders]);



    }
}
