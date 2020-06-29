<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    public function index(Request $request){
        $orders=Order::with(['details.entity', 'customer', 'details.clinic'])->where('status', '!=', 'pending')->orderBy('id', 'desc')->paginate(20);

        return view('admin.order.index', compact('orders'));

    }

    public function details(Request $request, $id){
        $order=Order::with(['details.entity', 'customer'])->where('status', '!=', 'pending')->find($id);
        return view('admin.order.details', compact('order'));
    }

}
