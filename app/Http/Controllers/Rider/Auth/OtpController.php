<?php

namespace App\Http\Controllers\Rider\Auth;

use App\Events\SendOtp;
use App\Models\Customer;
use App\Models\OTPModel;
use App\Models\Rider;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
            'mobile'=>'required|string|digits:10|exists:riders',
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

    protected function verifyRegister(Request $request){
        $user=Rider::where('mobile', $request->mobile)->first();
        if($user->status==0){
            if(OTPModel::verifyOTP('riders',$user->id,$request->type,$request->otp)){

                $user->status=1;
                $user->save();

                return [
                    'status'=>'success',
                    'message'=>'OTP has been verified successfully',
                    'token'=>Auth::guard('riderapi')->fromUser($user)
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
        $user=Rider::where('mobile', $request->mobile)->first();
        if(in_array($user->status, [0,1])){
            if(OTPModel::verifyOTP('riders',$user->id,$request->type,$request->otp)){

                $user->status=1;
                $user->save();

                return [
                    'status'=>'success',
                    'message'=>'OTP has been verified successfully',
                    'token'=>Auth::guard('riderapi')->fromUser($user)
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

    protected function verifyResetPassword(Request $request){
        $user=Rider::where('mobile', $request->mobile)->first();
        if(in_array($user->status, [0,1])){
            if(OTPModel::verifyOTP('riders',$user->id,$request->type,$request->otp)){

                $user->status=1;
                $user->save();

                return [
                    'status'=>'success',
                    'message'=>'OTP Has Been Verified',
                    'token'=>Auth::guard('riderapi')->fromUser($user)
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
            'mobile'=>'required|string|digits:10|exists:riders',
        ]);

        $user=Rider::where('mobile', $request->mobile)->first();
        if(in_array($user->status, [0,1])){
                $otp=OTPModel::createOTP('riders', $user->id, $request->type);
                $msg=str_replace('{{otp}}', $otp, config('sms-templates.'.$request->type));
                event(new SendOtp($user->mobile, $msg));
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
