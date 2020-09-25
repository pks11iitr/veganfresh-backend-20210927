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
$api->post('forgot', 'Customer\Auth\ForgotPasswordController@sendResetOTP');
$api->post('update-password', 'Customer\Auth\ForgotPasswordController@updatePassword');
$api->post('verify-otp', 'Customer\Auth\OtpController@verify');
$api->post('resend-otp', 'Customer\Auth\OtpController@resend');

//home page api
$api->get('home', 'Customer\Api\HomeController@home');
//<*************************************** hallobasket************************************>
//profile

$api->get('profile', ['as'=>'profile', 'uses'=>'Customer\Api\ProfileController@view']);
$api->post('update-profile', ['as'=>'profile', 'uses'=>'Customer\Api\ProfileController@update']);

$api->get('category', ['as'=>'category.list', 'uses'=>'Customer\Api\CategoryController@category']);
$api->get('sub-category/{id}', ['as'=>'category-subcategory.list', 'uses'=>'Customer\Api\CategoryController@subcategory']);
$api->get('products', ['as'=>'products.list', 'uses'=>'Customer\Api\ProductController@products']);
$api->get('search-products', ['as'=>'products.search', 'uses'=>'Customer\Api\ProductController@search_products']);
$api->post('add-favorite-product', ['as'=>'products.addfavorite', 'uses'=>'Customer\Api\FavoriteProductController@add_favorite_product']);
$api->get('offer-products', ['as'=>'products.offer', 'uses'=>'Customer\Api\OfferProductController@offerproducts']);

$api->get('offer-products-without-cat', ['as'=>'products.offer', 'uses'=>'Customer\Api\OfferProductController@offerproducts_withoutcategory']);
//cart
$api->post('add-cart', ['as'=>'add.cart', 'uses'=>'Customer\Api\CartController@store']);
$api->get('cart-details', ['as'=>'cart.detail', 'uses'=>'Customer\Api\CartController@getCartDetails']);
$api->get('customer-address', ['as'=>'delivery.address', 'uses'=>'Customer\Api\CustomerAddressController@getcustomeraddress']);
$api->post('add-customer-address', ['as'=>'add.delivery.address', 'uses'=>'Customer\Api\CustomerAddressController@addcustomeraddress']);
$api->get('product-detail/{id}', ['as'=>'product.detail', 'uses'=>'Customer\Api\ProductController@product_detail']);
$api->post('save-later-product', ['as'=>'savelater.product', 'uses'=>'Customer\Api\SavelaterProductController@savelater_product']);

$api->get('favorite-product', ['as'=>'product.favorite', 'uses'=>'Customer\Api\FavoriteProductController@list_favorite_product']);

//wallet apis
$api->get('wallet-balance', ['as'=>'wallet.balance', 'uses'=>'Customer\Api\WalletController@getWalletBalance']);
$api->get('wallet-history', ['as'=>'wallet.history', 'uses'=>'Customer\Api\WalletController@history']);

$api->post('recharge', ['as'=>'wallet.add.money', 'uses'=>'Customer\Api\WalletController@addMoney']);
$api->post('verify-recharge', ['as'=>'wallet.add.money', 'uses'=>'Customer\Api\WalletController@verifyRecharge']);
///order
$api->post('initiate-order', ['as'=>'initiate.order', 'uses'=>'Customer\Api\OrderController@initiateOrder']);
$api->get('product-reviews/{id}', ['as'=>'product.reviews', 'uses'=>'Customer\Api\ReviewController@index']);

$api->group(['middleware' => ['customer-auth']], function ($api) {

    $api->post('add-delivery-address/{order_id}', ['as'=>'order.delivery.address', 'uses'=>'Customer\Api\OrderController@selectAddress']);

    $api->post('apply-coupon/{order_id}', ['as'=>'order.apply.coupon', 'uses'=>'Customer\Api\OrderController@applyCoupon']);

    $api->get('get-payment-info/{order_id}', ['as'=>'order.payment.info', 'uses'=>'Customer\Api\OrderController@getPaymentInfo']);

    $api->get('orders-history', ['as'=>'order.history', 'uses'=>'Customer\Api\OrderController@index']);

    $api->post('initiate-payment/{order_id}', ['as'=>'payment.initiate', 'uses'=>'Customer\Api\PaymentController@initiatePayment']);

    $api->post('verify-payment', ['as'=>'payment.verify', 'uses'=>'Customer\Api\PaymentController@verifyPayment']);

    $api->get('order-details/{order_id}', ['as'=>'order.details', 'uses'=>'Customer\Api\OrderController@orderdetails']);

    $api->get('cancel-order/{order_id}', ['as'=>'order.cancel', 'uses'=>'Customer\Api\OrderController@cancelOrder']);


});

//Rider api Start


$api->group(['prefix' => 'rider'], function ($api) {
    $api->post('login', 'Rider\Auth\LoginController@login');
    $api->post('login-with-otp', 'Rider\Auth\LoginController@loginWithOtp');
    $api->post('forgot', 'Rider\Auth\ForgotPasswordController@sendResetOTP');
    $api->post('rider-update-password', 'Rider\Auth\ForgotPasswordController@updatePassword');

    $api->post('verify-otp', 'Rider\Auth\OtpController@verify');
    $api->post('resend-otp', 'Rider\Auth\OtpController@resend');

    $api->group(['middleware' => ['rider-auth']], function ($api) {

        $api->get('rider-orders-history', ['as'=>'rider.order.history', 'uses'=>'Rider\Api\RiderOrderController@index']);
        $api->get('rider-order-details/{order_id}', ['as'=>'rider.order.details', 'uses'=>'Rider\Api\RiderOrderController@orderdetails']);

        $api->get('delivery-order/{order_id}', ['as'=>'rider.order.delivered', 'uses'=>'Rider\Api\RiderOrderController@markDelivered']);

        $api->get('return-order/{order_id}', ['as'=>'rider.item.return', 'uses'=>'Rider\Api\RiderOrderController@returnProduct']);

    });

});
