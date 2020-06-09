<?php

namespace App\Http\Controllers\Customer\Auth;

use App\Events\CustomerRegistered;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:100', 'unique:customers'],
            'password' => ['required', 'string', 'min:6'],
            'mobile'=>['required', 'string', 'max:10', 'unique:customers']
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        return Customer::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'mobile'=>$data['mobile'],
        ]);
    }

    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        event(new CustomerRegistered($user = $this->create($request->all())));

        return [
            'status'=>'success',
            'message'=>'Please verify otp to continue'
        ];
    }
}
