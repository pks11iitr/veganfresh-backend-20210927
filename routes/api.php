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


$api->get('profile', ['as'=>'profile', 'uses'=>'Customer\Api\ProfileController@view']);
$api->post('profile', ['as'=>'profile', 'uses'=>'Customer\Api\ProfileController@update']);


//Order apis
$api->post('initiate-order', ['as'=>'initiate.order', 'uses'=>'Customer\Api\OrderController@initiateOrder']);

$api->get('order-details/{id}', ['as'=>'order.details', 'uses'=>'Customer\Api\OrderController@orderdetails']);

$api->get('order-history', ['as'=>'order.history', 'uses'=>'Customer\Api\OrderController@index']);

$api->get('cancel-order/{id}', ['as'=>'order.cancel', 'uses'=>'Customer\Api\OrderController@cancelOrder']);

$api->post('reschedule-order/{id}', ['as'=>'order.reschedule', 'uses'=>'Customer\Api\OrderController@rescheduleOrder']);



$api->post('update-contact/{id}', ['as'=>'order.contact.update', 'uses'=>'Customer\Api\OrderController@addContactDetails']);

$api->post('update-cart', ['as'=>'cart.update', 'uses'=>'Customer\Api\CartController@updateCartItems']);
$api->get('cart-details', ['as'=>'cart.details', 'uses'=>'Customer\Api\CartController@index']);


//payment apis
$api->post('initiate-payment/{id}', ['as'=>'order.payment', 'uses'=>'Customer\Api\PaymentController@initiatePayment']);
$api->post('verify-payment', ['as'=>'order.payment.verify', 'uses'=>'Customer\Api\PaymentController@verifyPayment']);


//wallet apis
$api->get('wallet-balance', ['as'=>'wallet.balance', 'uses'=>'Customer\Api\WalletController@getWalletBalance']);
$api->get('wallet-history', ['as'=>'wallet.history', 'uses'=>'Customer\Api\WalletController@history']);

$api->post('recharge', ['as'=>'wallet.add.money', 'uses'=>'Customer\Api\WalletController@addMoney']);
$api->post('verify-recharge', ['as'=>'wallet.add.money', 'uses'=>'Customer\Api\WalletController@verifyRecharge']);

//complaints api
$api->get('complaints', ['as'=>'complaints.list', 'uses'=>'Customer\Api\ComplaintController@index']);
$api->post('complaints', ['as'=>'complaints.list', 'uses'=>'Customer\Api\ComplaintController@create']);
$api->get('complaint/{id}', ['as'=>'complaints.list', 'uses'=>'Customer\Api\ComplaintController@messages']);
$api->post('complaint/{id}', ['as'=>'complaints.list', 'uses'=>'Customer\Api\ComplaintController@postMessage']);
