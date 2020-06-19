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
$api->get('clinics', ['as'=>'clinics.list', 'uses'=>'Customer\Api\ClinicController@index']);
$api->get('therapies', ['as'=>'therapies.list', 'uses'=>'Customer\Api\TherapyController@index']);
$api->get('clinic/{id}', ['as'=>'clinics.details', 'uses'=>'Customer\Api\ClinicController@details']);
$api->get('clinic-therapy/{clinicid}/{therapyid}', ['as'=>'clinics.therapy.details', 'uses'=>'Customer\Api\ClinicController@clinicTherapyDetails']);
$api->get('therapy/{id}', ['as'=>'therapy.details', 'uses'=>'Customer\Api\TherapyController@details']);

$api->get('products-home', ['as'=>'products.home', 'uses'=>'Customer\Api\ProductController@home']);
$api->get('product/{id}', ['as'=>'product.details', 'uses'=>'Customer\Api\ProductController@details']);
$api->get('topdeals-products', ['as'=>'product.deals', 'uses'=>'Customer\Api\ProductController@topdeals']);
$api->get('bestseller-products', ['as'=>'product.deals', 'uses'=>'Customer\Api\ProductController@bestseller']);

$api->get('reviews/{type}/{id}', ['as'=>'reviews', 'uses'=>'Customer\Api\ReviewController@index']);

$api->get('search', ['as'=>'search', 'uses'=>'Customer\Api\SearchController@index']);
