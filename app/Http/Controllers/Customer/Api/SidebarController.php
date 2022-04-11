<?php

namespace App\Http\Controllers\Customer\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Configuration;
class SidebarController extends Controller
{


    public function referDetails(Request $request){



        $user = $request->user;

        $user_id=$request->user->id??'';

        $config = Configuration::where('param', 'refer_amount')
            ->first();

        $referral_amount = $config->value??0;

        if($referral_amount>0)
            $conditions=[
                'Referral amount of Rs. '.($referral_amount).' will be credited on first order',
                //'Referral amount of Rs. '.($referral_amount/2).' will be credited on second order',
               // 'Your friends will be get Rs. 51 as a welcome bonus on registration.'
            ];
        else{
            $conditions=[
            ];
        }

//        $share = Banner::where('type', 'share')
//            ->first();

        $refer_link= [
            'link'=>!empty($user)?$user->getDynamicLink():'https://play.google.com/store/apps/details?id=com.vegansFresh.vegansFresh',
           // 'image'=>$share->image??'',
            'product_text'=>"I am using Vegansfresh app for online purchase . Use my refferal link & you will get Rs.$referral_amount in your wallet.",
            'app_text'=>'Order Now'
            //'qr_image'=>'https://images.freekaamaal.com/featured_images/174550_beereebn.png'
        ];

        return [
            'status'=>'success',
            'action'=>'',
            'display_message'=>'',
            'data'=>compact('referral_amount', 'user_id','refer_link')
        ];

    }


}
