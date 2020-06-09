<?php

use Illuminate\Http\Request;
$api = app('Dingo\Api\Routing\Router');

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

$api->post('login', 'Customer\Auth\LoginController@login');
$api->post('login-with-otp', 'Customer\Auth\LoginController@loginWithOtp');
$api->post('register', 'Customer\Auth\RegisterController@register');
$api->post('forgot', 'Customer\Auth\ForgotPasswordController@forgot');
$api->post('verify-otp', 'Customer\Auth\OtpController@verify');
$api->post('resend-otp', 'Customer\Auth\OtpController@resend');


$api->get('home', 'Customer\Api\HomeController@home');
$api->get('clinics', ['as'=>'clinics.list', 'uses'=>'Customer\Api\ClinicsController@index']);
$api->get('therapies', ['as'=>'therapy.list', 'uses'=>'Customer\Api\TherapyController@index']);
