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

//home page api
$api->get('home', 'Customer\Api\HomeController@home');
//<*************************************** hallobasket************************************>
$api->get('category', ['as'=>'category.list', 'uses'=>'Customer\Api\CategoryController@category']);
$api->get('sub-category/{id}', ['as'=>'category-subcategory.list', 'uses'=>'Customer\Api\CategoryController@subcategory']);
$api->get('products', ['as'=>'products.list', 'uses'=>'Customer\Api\ProductController@products']);
$api->get('search-products', ['as'=>'products.search', 'uses'=>'Customer\Api\ProductController@search_products']);
$api->post('add-favorite-product', ['as'=>'products.addfavorite', 'uses'=>'Customer\Api\FavoriteProductController@add_favorite_product']);
$api->get('offer-products', ['as'=>'products.offer', 'uses'=>'Customer\Api\OfferProductController@offerproducts']);
