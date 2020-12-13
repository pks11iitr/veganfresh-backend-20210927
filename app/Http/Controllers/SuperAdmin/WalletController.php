<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\Order;
use App\Models\Wallet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WalletController extends Controller
{
    public function getbalance(Request $request, $user_id){

        return [

            'status'=>'success',
            'data'=>[

                'cashback'=>Wallet::points($user_id)??0,
                'balance'=>Wallet::balance($user_id)??0,

            ]

        ];

    }


    public function addremove(Request $request){

        $request->validate([

            'order_id'=>'required',
            'amount_type'=>'required|in:cashback,balance',
            'calculation_type'=>'required|in:fixed,percentage',
            'action_type'=>'required|in:add,revoke',
            'amount'=>'required|integer|min:1',
            'wallet_text'=>'required'

        ]);

        $order=Order::findOrFail($request->order_id);

        if($request->action_type=='add'){

            if($request->calculation_type=='fixed'){

                if($request->amount_type=='cashback'){

                    Wallet::updatewallet($order->user_id, $request->wallet_text, 'Credit', $request->amount, 'POINT', $request->order_id);
                    return $request->all();
                    return redirect()->back()->with('success', 'Wallet has been updated');

                }else if($request->amount_type=='balance'){
                    Wallet::updatewallet($order->user_id, $request->wallet_text, 'Credit', $request->amount, 'CASH', $request->order_id);
                    return redirect()->back()->with('success', 'Wallet has been updated');
                }

            }else if($request->calculation_type=='percentage'){

                $amount=intval(($order->total_cost*$request->amount)/100);
                if($amount>0){
                    if($request->amount_type=='cashback'){
                        Wallet::updatewallet($order->user_id, $request->wallet_text, 'Credit', $amount, 'POINT', $request->order_id);
                        return redirect()->back()->with('success', 'Wallet has been updated');
                    }else if($request->amount_type=='balance'){
                        Wallet::updatewallet($order->user_id, $request->wallet_text, 'Credit', $amount, 'CASH', $request->order_id);
                        return redirect()->back()->with('success', 'Wallet has been updated');
                    }
                }

                return redirect()->back()->with('error', 'Amount to be added must be geater than 0');

            }



        }else if($request->action_type=='revoke'){

            if($request->calculation_type=='fixed'){

                if($request->amount_type=='cashback'){
                    Wallet::updatewallet($order->user_id, $request->wallet_text, 'Debit', $request->amount, 'POINT', $request->order_id);

                    return redirect()->back()->with('success', 'Wallet has been updated');

                }else if($request->amount_type=='balance'){
                    Wallet::updatewallet($order->user_id, $request->wallet_text, 'Debit', $request->amount, 'CASH', $request->order_id);
                    return redirect()->back()->with('success', 'Wallet has been updated');
                }

            }else if($request->calculation_type=='percentage'){

                $amount=intval(($order->total_cost*$request->amount)/100);
                if($amount>0){
                    if($request->amount_type=='cashback'){
                        Wallet::updatewallet($order->user_id, $request->wallet_text, 'Debit', $amount, 'POINT', $request->order_id);
                        return redirect()->back()->with('success', 'Wallet has been updated');
                    }else if($request->amount_type=='balance'){
                        Wallet::updatewallet($order->user_id, $request->wallet_text, 'Debit', $amount, 'CASH', $request->order_id);
                        return redirect()->back()->with('success', 'Wallet has been updated');
                    }
                }

                return redirect()->back()->with('error', 'Amount to be added must be geater than 0');

            }
        }

        return redirect()->back()->with('error', 'Invalid Request');

    }

    public function getWalletHistory(Request $request, $user_id){

        $balance_history=Wallet::where('user_id', $user_id)
            ->where('iscomplete', true)
            ->where('amount_type', 'CASH')
            ->orderBy('id', 'desc')
            ->get();
        $cashback_history=Wallet::where('user_id', $user_id)
            ->where('iscomplete', true)
            ->where('amount_type', 'POINT')
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.wallet.wallet-history', compact('user_id', 'balance_history', 'cashback_history'));

    }

}
