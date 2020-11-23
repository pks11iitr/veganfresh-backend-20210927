<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Exports\OrdersExport;
use App\Exports\SalesExport;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Excel;


class ReportDownloader extends Controller
{
    public function downloadSalesReport(Request $request){

        $orders=Order::with(['deliveryaddress','customer', 'rider', 'storename', 'timeslot'])
        ->where('status', '!=', 'pending');

        if(isset($request->search)){
            $orders=$orders->where(function($orders) use ($request){

                $orders->where('name', 'like', "%".$request->search."%")
                    ->orWhere('email', 'like', "%".$request->search."%")
                    ->orWhere('mobile', 'like', "%".$request->search."%")
                    ->orWhere('refid', 'like', "%".$request->search."%")
                    ->orWhereHas('customer', function($customer)use( $request){
                        $customer->where('name', 'like', "%".$request->search."%")
                            ->orWhere('email', 'like', "%".$request->search."%")
                            ->orWhere('mobile', 'like', "%".$request->search."%");
                    });
            });

        }

        if($request->fromdate)
            $orders=$orders->where('delivery_date', '>=', $request->fromdate);


        if($request->todate)
            $orders=$orders->where('delivery_date', '<=', $request->todate);

        if($request->status)
            $orders=$orders->where('status', $request->status);

        if($request->payment_status)
            $orders=$orders->where('payment_status', $request->payment_status);

        if($request->store_id)
            $orders=$orders->where('store_id', $request->store_id);

        if($request->rider_id)
            $orders=$orders->where('rider_id', $request->rider_id);

        if($request->ordertype)
            $orders=$orders->orderBy('created_at', $request->ordertype);

        if($request->delivery_slot)
            $orders=$orders->orderBy('delivery_slot', $request->delivery_slot);

        $orders=$orders->get();

        //return $orders;

        return Excel::download(new SalesExport($orders), 'sales.xlsx');



    }


    public function downloadOrderReport(Request $request){
        $orders=Order::with(['deliveryaddress','customer', 'rider', 'storename', 'timeslot', 'details.entity', 'details.size'])
            ->where('status', '!=', 'pending');

        if(isset($request->search)){
            $orders=$orders->where(function($orders) use ($request){

                $orders->where('name', 'like', "%".$request->search."%")
                    ->orWhere('email', 'like', "%".$request->search."%")
                    ->orWhere('mobile', 'like', "%".$request->search."%")
                    ->orWhere('refid', 'like', "%".$request->search."%")
                    ->orWhereHas('customer', function($customer)use( $request){
                        $customer->where('name', 'like', "%".$request->search."%")
                            ->orWhere('email', 'like', "%".$request->search."%")
                            ->orWhere('mobile', 'like', "%".$request->search."%");
                    });
            });

        }

        if($request->fromdate)
            $orders=$orders->where('delivery_date', '>=', $request->fromdate);


        if($request->todate)
            $orders=$orders->where('delivery_date', '<=', $request->todate);

        if($request->status)
            $orders=$orders->where('status', $request->status);

        if($request->payment_status)
            $orders=$orders->where('payment_status', $request->payment_status);

        if($request->store_id)
            $orders=$orders->where('store_id', $request->store_id);

        if($request->rider_id)
            $orders=$orders->where('rider_id', $request->rider_id);

        if($request->ordertype)
            $orders=$orders->orderBy('created_at', $request->ordertype);

        if($request->delivery_slot)
            $orders=$orders->orderBy('delivery_slot', $request->delivery_slot);

        $orders=$orders->get();

        //return $orders;

        return Excel::download(new OrdersExport($orders), 'orders.xlsx');
    }

}
