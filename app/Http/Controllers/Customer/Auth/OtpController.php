<?php

namespace App\Http\Controllers\Customer\Auth;

use App\Events\SendOtp;
use App\Models\Customer;
use App\Models\OTPModel;
use App\Models\Configuration;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\SMS\ConnectExpress;
use Illuminate\Support\Facades\Auth;
class OtpController extends Controller
{

    /**
     * Handle a login request to the application with otp.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */

    public function verify(Request $request){
        $request->validate([
            'type'=>'required|string|max:15',
            'mobile'=>'required|string|digits:10|exists:customers',
            'otp'=>'required|digits:6'
        ]);

        switch($request->type){
            case 'register': return $this->verifyRegister($request);
            case 'login': return $this->verifyLogin($request);
            case 'reset': return $this->verifyResetPassword($request);
        }

        return [
            'status'=>'failed',
            'message'=>'Request is not valid'
        ];
    }
    protected function verifyResetPassword(Request $request){
        $user=Customer::where('mobile', $request->mobile)->first();
        if(in_array($user->status, [0,1])){
            if(OTPModel::verifyOTP('customer',$user->id,$request->type,$request->otp)){

                $user->status=1;
                $user->save();

                return [
                    'status'=>'success',
                    'message'=>'OTP Has Been Verified',
                    'token'=>Auth::guard('customerapi')->fromUser($user)
                ];
            }

            return [
                'status'=>'failed',
                'message'=>'OTP is not correct',
                'token'=>''
            ];

        }
        return [
            'status'=>'failed',
            'message'=>'Account has been blocked',
            'token'=>''
        ];
    }

    protected function verifyRegister(Request $request){
         $user=Customer::where('mobile', $request->mobile)->first();
        if($user->status==0){
            if(OTPModel::verifyOTP('customer',$user->id,$request->type,$request->otp)){
               $user->notification_token=$request->token;
               $user->status=1;
               $user->save();

                  $welcome_bonus=Configuration::where('param','welcome_bonus')->first();

                  $msg=str_replace('{#var#}', $welcome_bonus->value, config('sms-templates.welcomeBonus'));
                  event(new SendOtp($user->mobile, $msg, env('LOGIN_OTP')));
                return [
                    'status'=>'success',
                    'message'=>'OTP has been verified successfully',
                    'token'=>Auth::guard('customerapi')->fromUser($user)
                ];
            }

            return [
                'status'=>'failed',
                'message'=>'OTP is not correct',
                'token'=>''
            ];

        }
        return [
            'status'=>'failed',
            'message'=>'Request is not valid',
            'token'=>''
        ];
    }


    protected function verifyLogin(Request $request){
        $user=Customer::where('mobile', $request->mobile)->first();

//        if($request->mobile=='1111111111' || $request->mobile=='8802035788')
//            return [
//                'status'=>'success',
//                'message'=>'OTP has been verified successfully',
//                'token'=>Auth::guard('customerapi')->fromUser($user),
//                'is_profile_complete'=>!empty($user->name)?1:0
//            ];

        if(in_array($user->status, [0,1])){
            if(OTPModel::verifyOTP('customer',$user->id,$request->type,$request->otp)){
                $user->notification_token=$request->token;
                $user->status=1;
                $user->save();
                
                 
            if($user->first_time==1){ 
                $welcome_bonus=Configuration::where('param','welcome_bonus')->first();
                $msg=str_replace('{#var#}', $welcome_bonus->value, config('sms-templates.welcomeBonus'));
                event(new SendOtp($user->mobile, $msg, env('LOGIN_OTP')));
                $user->first_time=2;
                $user->save();
            }
                 
                return [
                    'status'=>'success',
                    'message'=>'OTP has been verified successfully',
                    'token'=>Auth::guard('customerapi')->fromUser($user),
                    'is_profile_complete'=>!empty($user->name)?1:0
                ];
            }

            return [
                'status'=>'failed',
                'message'=>'OTP is not correct',
                'token'=>''
            ];

        }
        return [
            'status'=>'failed',
            'message'=>'Account has been blocked',
            'token'=>''
        ];
    }


    public function resend(Request $request){
        $request->validate([
            'type'=>'required|string|max:15',
            'mobile'=>'required|string|digits:10|exists:customers',
        ]);

        $user=Customer::where('mobile', $request->mobile)->first();
        if(in_array($user->status, [0,1])){
                $otp=OTPModel::createOTP('customer', $user->id, $request->type);
                $msg=str_replace('{{otp}}', $otp, config('sms-templates.'.$request->type));
                event(new SendOtp($user->mobile, $msg, env('LOGIN_OTP')));
                return [
                    'status'=>'success',
                    'message'=>'Please verify OTP to continue',
                ];
        }

        return [
            'status'=>'failed',
            'message'=>'Account has been blocked',
        ];

    }

}
