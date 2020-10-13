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

Route::get('/home', function () {
    return redirect()->route('home');
});

Auth::routes();

Route::group(['middleware'=>['auth', 'acl'], 'is'=>'admin|store'], function(){

    Route::get('/role-check', 'SuperAdmin\HomeController@check_n_redirect')->name('user.role.check');

});


Route::group(['middleware'=>['auth', 'acl'], 'is'=>'admin'], function(){

    Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');

    Route::get('/dashboard', 'SuperAdmin\DashboardController@index')->name('home');

//****************************************hallobasket********************************************
    Route::group(['prefix'=>'configurations'], function(){
        Route::get('/','SuperAdmin\ConfigurationController@index')->name('configurations.list');
        Route::post('/','SuperAdmin\ConfigurationController@update');
    });


   Route::group(['prefix'=>'banners'], function(){
        Route::get('/','SuperAdmin\BannerController@index')->name('banners.list');
        Route::get('create','SuperAdmin\BannerController@create')->name('banners.create');
        Route::post('store','SuperAdmin\BannerController@store')->name('banners.store');
        Route::get('edit/{id}','SuperAdmin\BannerController@edit')->name('banners.edit');
        Route::post('update/{id}','SuperAdmin\BannerController@update')->name('banners.update');
        Route::get('delete/{id}','SuperAdmin\BannerController@delete')->name('banners.delete');
    });

    Route::group(['prefix'=>'category'], function(){
        Route::get('/','SuperAdmin\CategoryController@index')->name('category.list');
        Route::get('create','SuperAdmin\CategoryController@create')->name('category.create');
        Route::post('store','SuperAdmin\CategoryController@store')->name('category.store');
        Route::get('edit/{id}','SuperAdmin\CategoryController@edit')->name('category.edit');
        Route::post('update/{id}','SuperAdmin\CategoryController@update')->name('category.update');

    });

    Route::group(['prefix'=>'subcategory'], function(){
        Route::get('/','SuperAdmin\SubCategoryController@index')->name('subcategory.list');
        Route::get('create','SuperAdmin\SubCategoryController@create')->name('subcategory.create');
        Route::post('store','SuperAdmin\SubCategoryController@store')->name('subcategory.store');
        Route::get('edit/{id}','SuperAdmin\SubCategoryController@edit')->name('subcategory.edit');
        Route::post('update/{id}','SuperAdmin\SubCategoryController@update')->name('subcategory.update');

    });

    Route::group(['prefix'=>'product'], function(){
        Route::get('/','SuperAdmin\ProductController@index')->name('product.list');
        Route::get('create','SuperAdmin\ProductController@create')->name('product.create');
        Route::post('store','SuperAdmin\ProductController@store')->name('product.store');
        Route::get('edit/{id}','SuperAdmin\ProductController@edit')->name('product.edit');
        Route::post('update/{id}','SuperAdmin\ProductController@update')->name('product.update');
        Route::post('product-sizeprice/{id}','SuperAdmin\ProductController@sizeprice')->name('product.sizeprice');
        Route::post('size-update','SuperAdmin\ProductController@updatesizeprice')->name('product.size.update');
        Route::get('size-images','SuperAdmin\ProductController@allimages')->name('product.size.images');

        Route::post('product-category-create/{id}','SuperAdmin\ProductController@productcategory')->name('product.category.create');
        Route::get('delete/{id}','SuperAdmin\ProductController@delete')->name('product.delete');

        Route::post('document/{id}','SuperAdmin\ProductController@document')->name('product.document');

        Route::get('bulk-upload','SuperAdmin\ProductController@bulk_upload_form')->name('product.bulk.form');
        Route::post('bulk-upload','SuperAdmin\ProductController@bulk_upload')->name('product.bulk.upload');


    });
    Route::group(['prefix'=>'homesection'], function(){
        Route::get('/','SuperAdmin\HomeSectionController@index')->name('homesection.list');
        Route::get('banner-create','SuperAdmin\HomeSectionController@bannercreate')->name('homesection.bannercreate');
        Route::post('banner-store','SuperAdmin\HomeSectionController@bannerstore')->name('homesection.bannerstore');
        Route::get('banner-edit/{id}','SuperAdmin\HomeSectionController@banneredit')->name('homesection.banneredit');
        Route::post('banner-update/{id}','SuperAdmin\HomeSectionController@bannerupdate')->name('homesection.bannerupdate');
        Route::get('productcreate','SuperAdmin\HomeSectionController@productcreate')->name('homesection.productcreate');
        Route::post('productstore','SuperAdmin\HomeSectionController@productstore')->name('homesection.productstore');
        Route::get('productedit/{id}','SuperAdmin\HomeSectionController@productedit')->name('homesection.productedit');
        Route::post('productupdate/{id}','SuperAdmin\HomeSectionController@productupdate')->name('homesection.productupdate');
        Route::post('productimage/{id}','SuperAdmin\HomeSectionController@productImage')->name('homesection.productimage');
        Route::get('productdelete/{id}','SuperAdmin\HomeSectionController@productdelete')->name('homesection.productdelete');
        Route::get('sub-category-create','SuperAdmin\HomeSectionController@subcategorycreate')->name('homesection.subcategorycreate');
        Route::post('sub-category-store','SuperAdmin\HomeSectionController@subcategorystore')->name('homesection.subcategorystore');
        Route::get('sub-category-edit/{id}','SuperAdmin\HomeSectionController@subcategoryedit')->name('homesection.subcategoryedit');
        Route::post('sub-category-update/{id}','SuperAdmin\HomeSectionController@subcategoryupdate')->name('homesection.subcategoryupdate');
        Route::post('subcategoryimage/{id}','SuperAdmin\HomeSectionController@subcategoryimage')->name('homesection.subcategoryimage');
        Route::get('subdelete/{id}','SuperAdmin\HomeSectionController@subdelete')->name('homesection.subdelete');
        Route::get('home-section-delete/{id}','SuperAdmin\HomeSectionController@homesectiondelete')->name('homesection.homesectiondelete');

    });

    Route::group(['prefix'=>'coupon'], function(){
        Route::get('/','SuperAdmin\CouponController@index')->name('coupon.list');
        Route::get('create','SuperAdmin\CouponController@create')->name('coupon.create');
        Route::post('store','SuperAdmin\CouponController@store')->name('coupon.store');
        Route::get('edit/{id}','SuperAdmin\CouponController@edit')->name('coupon.edit');
        Route::post('update/{id}','SuperAdmin\CouponController@update')->name('coupon.update');

    });

    Route::group(['prefix'=>'orders'], function(){
        Route::get('/','SuperAdmin\OrderController@index')->name('orders.list');
        Route::get('details/{id}','SuperAdmin\OrderController@details')->name('order.details');
        //Route::get('product','SuperAdmin\OrderController@product')->name('orders.product');
        Route::get('change-status/{id}','SuperAdmin\OrderController@changeStatus')->name('order.status.change');
        Route::get('change-payment-status/{id}','SuperAdmin\OrderController@changePaymentStatus')->name('payment.status.change');
        Route::post('changeRider/{id}','SuperAdmin\OrderController@changeRider')->name('rider.change');
        Route::get('add-cashback/{id}/{type}','SuperAdmin\OrderController@addCashback')->name('add.cashback');

    });

    Route::group(['prefix'=>'returnproduct'], function(){
        Route::get('/','SuperAdmin\ReturnProductController@index')->name('return.product.list');

    });

    Route::group(['prefix'=>'sales'], function(){
        Route::get('/','SuperAdmin\SalesController@index')->name('sales.list');

    });


    Route::group(['prefix'=>'inventory'], function(){
        Route::get('packets','SuperAdmin\InventoryController@packet')->name('packets.list');
        Route::get('quantity','SuperAdmin\InventoryController@quantity')->name('quantity.list');

    });


    Route::group(['prefix'=>'timeslot'], function(){
        Route::get('/','SuperAdmin\TimeSlotController@index')->name('timeslot.list');
        Route::get('create','SuperAdmin\TimeSlotController@create')->name('timeslot.create');
        Route::post('store','SuperAdmin\TimeSlotController@store')->name('timeslot.store');
        Route::get('edit/{id}','SuperAdmin\TimeSlotController@edit')->name('timeslot.edit');
        Route::post('update/{id}','SuperAdmin\TimeSlotController@update')->name('timeslot.update');

    });

    Route::group(['prefix'=>'area'], function(){
        Route::get('/','SuperAdmin\AreaController@index')->name('area.list');
        Route::get('create','SuperAdmin\AreaController@create')->name('area.create');
        Route::post('store','SuperAdmin\AreaController@store')->name('area.store');
        Route::get('edit/{id}','SuperAdmin\AreaController@edit')->name('area.edit');
        Route::post('update/{id}','SuperAdmin\AreaController@update')->name('area.update');
        Route::post('import','SuperAdmin\AreaController@import')->name('area.import');

    });

    Route::group(['prefix'=>'rider'], function(){
        Route::get('/','SuperAdmin\RiderController@index')->name('rider.list');
        Route::get('create','SuperAdmin\RiderController@create')->name('rider.create');
        Route::post('store','SuperAdmin\RiderController@store')->name('rider.store');
        Route::get('edit/{id}','SuperAdmin\RiderController@edit')->name('rider.edit');
        Route::post('update/{id}','SuperAdmin\RiderController@update')->name('rider.update');

    });

    Route::group(['prefix'=>'stores'], function(){
        Route::get('/','SuperAdmin\StoreController@index')->name('stores.list');
        Route::get('create','SuperAdmin\StoreController@create')->name('stores.create');
        Route::post('store','SuperAdmin\StoreController@store')->name('stores.store');
        Route::get('edit/{id}','SuperAdmin\StoreController@edit')->name('stores.edit');
        Route::post('update/{id}','SuperAdmin\StoreController@update')->name('stores.update');

    });
   //****************************************end*************************************************
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
        Route::post('send_message','SuperAdmin\CustomerController@send_message')->name('customer.send_message');
    });

    Route::group(['prefix'=>'complain'], function(){
        Route::get('/','SuperAdmin\ComplainController@index')->name('complain.list');
        Route::get('view/{id}','SuperAdmin\ComplainController@details')->name('complain.view');
        Route::post('message','SuperAdmin\ComplainController@send_message')->name('complain.message');
        Route::get('mark-closed/{id}','SuperAdmin\ComplainController@markAsClosed')->name('complain.close');

    });

    Route::group(['prefix'=>'news'], function(){
        Route::get('/','SuperAdmin\NewsUpdateController@index')->name('news.list');
        Route::get('create','SuperAdmin\NewsUpdateController@create')->name('news.create');
        Route::post('store','SuperAdmin\NewsUpdateController@store')->name('news.store');
        Route::get('edit/{id}','SuperAdmin\NewsUpdateController@edit')->name('news.edit');
        Route::post('update/{id}','SuperAdmin\NewsUpdateController@update')->name('news.update');

    });

    Route::group(['prefix'=>'notification'], function(){
        Route::get('create','SuperAdmin\NotificationController@create')->name('notification.create');
        Route::post('store','SuperAdmin\NotificationController@store')->name('notification.store');

    });

    Route::group(['prefix'=>'wallet'], function(){

        Route::post('add-remove-wallet-balance', 'SuperAdmin\WalletController@addremove')->name('wallet.add.remove');

        Route::get('get-wallet-balance/{id}', 'SuperAdmin\WalletController@getbalance')->name('user.wallet.balance');

    });


});

Route::group(['prefix'=>'api'], function() {
    Route::get('privacy-policy', 'SuperAdmin\PolicyController@index')->name('policy.view');
    Route::get('terms-condition', 'SuperAdmin\PolicyController@terms')->name('terms.view');
    Route::get('about-us', 'SuperAdmin\PolicyController@about')->name('about.view');
    Route::get('invoice/{id}', 'SuperAdmin\PolicyController@invoice')->name('invoice.view');
});


Route::group(['prefix'=>'store-admin', 'middleware'=>['auth', 'acl'], 'is'=>'store'], function() {

    Route::get('/dashboard', 'StoreAdmin\DashboardController@index')->name('storeadmin.home');


    Route::group(['prefix'=>'orders'], function(){
        Route::get('/','StoreAdmin\OrderController@index')->name('storeadmin.orders.list');
        Route::get('details/{id}','StoreAdmin\OrderController@details')->name('storeadmin.order.details');

        Route::get('change-status/{id}','SuperAdmin\OrderController@changeStatus')->name('storeadmin.order.status.change');

        Route::post('changeRider/{id}','SuperAdmin\OrderController@changeRider')->name('storeadmin.rider.change');

    });

    Route::group(['prefix'=>'rider'], function() {
        Route::get('/', 'StoreAdmin\RiderController@index')->name('storeadmin.rider.list');
        Route::get('create', 'StoreAdmin\RiderController@create')->name('storeadmin.rider.create');
        Route::post('store', 'StoreAdmin\RiderController@store')->name('storeadmin.rider.store');
        Route::get('edit/{id}', 'StoreAdmin\RiderController@edit')->name('storeadmin.rider.edit');
        Route::post('update/{id}', 'StoreAdmin\RiderController@update')->name('storeadmin.rider.update');
    });


});

//Route::group(['prefix'=>'riders', 'middleware'=>['auth', 'acl'], 'is'=>'rider-admin'], function() {
    //Route::get('/dashboard', 'RiderAdmin\DashboardController@index')->name('rideradmin.home');

    //Route::group(['prefix'=>'orders'], function(){
       // Route::get('/','RiderAdmin\OrderController@index')->name('rider.orders.list');
        //Route::get('details/{id}','SuperAdmin\OrderController@details')->name('rider.order.details');


//});

