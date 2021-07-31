<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\ComplaintMessage;
use App\Models\Invoice;
use App\Models\Order;
use PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Storage;

class PolicyController extends Controller
{
    public function index(Request $request){

        return view('admin.contenturl.privacy_policy');
    }
    public function terms(Request $request){

        return view('admin.contenturl.termscondition');
    }
    public function about(Request $request){

        return view('admin.contenturl.aboutus');
    }

//    public function invoice(Request $request){
//
//        return view('admin.contenturl.invoice');
//    }

    public function invoice($id){
        $orders = Order::with(['details.entity','details.size', 'customer', 'deliveryaddress'])->find($id);
       // var_dump($orders);die();
        $invoice=Invoice::find(1);
     $pdf = PDF::loadView('admin.contenturl.newinvoice', compact('orders','invoice'))->setPaper('a4', 'portrait');
     return $pdf->download('invoice.pdf');
        //return view('admin.contenturl.newinvoice',['orders'=>$orders, 'invoice'=>$invoice]);
    }

}
