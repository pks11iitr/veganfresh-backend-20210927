<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::group(['middle'=>['auth', 'acl'], 'is'=>'admin'], function(){
    Route::get('/home', 'SuperAdmin\HomeController@index')->name('home');

   Route::group(['prefix'=>'banners'], function(){
        Route::get('/','SuperAdmin\BannerController@index')->name('banners.list');
        Route::get('create','SuperAdmin\BannerController@create')->name('banners.create');
        Route::post('store','SuperAdmin\BannerController@store')->name('banners.store');
        Route::get('edit/{id}','SuperAdmin\BannerController@edit')->name('banners.edit');
        Route::post('update/{id}','SuperAdmin\BannerController@update')->name('banners.update');
        Route::get('delete/{id}','SuperAdmin\BannerController@delete')->name('banners.delete');
    });
   Route::group(['prefix'=>'therapy'], function(){
        Route::get('/','SuperAdmin\TherapistController@index')->name('therapy.list');
        Route::get('create','SuperAdmin\TherapistController@create')->name('therapy.create');
        Route::post('store','SuperAdmin\TherapistController@store')->name('therapy.store');
        Route::get('edit/{id}','SuperAdmin\TherapistController@edit')->name('therapy.edit');
        Route::post('update/{id}','SuperAdmin\TherapistController@update')->name('therapy.update');
        Route::post('document/{id}','SuperAdmin\TherapistController@document')->name('therapy.document');
        Route::get('delete/{id}','SuperAdmin\TherapistController@delete')->name('therapy.delete');
    });
  Route::group(['prefix'=>'clinic'], function(){
        Route::get('/','SuperAdmin\ClinicController@index')->name('clinic.list');
        Route::get('create','SuperAdmin\ClinicController@create')->name('clinic.create');
        Route::post('store','SuperAdmin\ClinicController@store')->name('clinic.store');
        Route::get('edit/{id}','SuperAdmin\ClinicController@edit')->name('clinic.edit');
        Route::post('update/{id}','SuperAdmin\ClinicController@update')->name('clinic.update');
        Route::post('document/{id}','SuperAdmin\ClinicController@document')->name('clinic.document');
        Route::get('delete/{id}','SuperAdmin\ClinicController@delete')->name('clinic.delete');
        Route::post('therapystore/{id}','SuperAdmin\ClinicController@therapystore')->name('clinic.therapystore');
        Route::get('therapyeedit/{id}','SuperAdmin\ClinicController@therapyedit')->name('clinic.therapyedit');
        Route::post('therapyeedit/{id}','SuperAdmin\ClinicController@therapyupdate');
    });
    Route::group(['prefix'=>'customer'], function(){
        Route::get('/','SuperAdmin\CustomerController@index')->name('customer.list');
        Route::get('edit/{id}','SuperAdmin\CustomerController@edit')->name('customer.edit');
        Route::post('update/{id}','SuperAdmin\CustomerController@update')->name('customer.update');
    });
   Route::group(['prefix'=>'product'], function(){
        Route::get('/','SuperAdmin\ProductController@index')->name('product.list');
        Route::get('create','SuperAdmin\ProductController@create')->name('product.create');
        Route::post('store','SuperAdmin\ProductController@store')->name('product.store');
        Route::get('edit/{id}','SuperAdmin\ProductController@edit')->name('product.edit');
        Route::post('update/{id}','SuperAdmin\ProductController@update')->name('product.update');
        Route::post('document/{id}','SuperAdmin\ProductController@document')->name('product.document');
        Route::get('delete/{id}','SuperAdmin\ProductController@delete')->name('product.delete');
    });

    Route::group(['prefix'=>'orders'], function(){
        Route::get('/','SuperAdmin\OrderController@index')->name('orders.list');
        Route::get('view/{id}','SuperAdmin\OrderController@details')->name('order.view');
    });

});

