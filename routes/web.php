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
    abort(403);
});

Route::get('/home', function () {
    return redirect()->route('home');
});

Auth::routes();

Route::group(['middleware'=>['auth', 'acl']], function(){

    Route::get('/role-check', 'SuperAdmin\HomeController@check_n_redirect')->name('user.role.check');

});


//Route::group(['middleware'=>['auth', 'acl'], 'is'=>'admin'], function(){
//
//    Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
//
//    Route::get('/dashboard', 'SuperAdmin\DashboardController@index')->name('home');
//
////****************************************hallobasket********************************************
//    Route::group(['prefix'=>'configurations'], function(){
//        Route::get('/','SuperAdmin\ConfigurationController@index')->name('configurations.list');
//        Route::post('/','SuperAdmin\ConfigurationController@update');
//    });
//
//
//   Route::group(['prefix'=>'banners'], function(){
//        Route::get('/','SuperAdmin\BannerController@index')->name('banners.list');
//        Route::get('create','SuperAdmin\BannerController@create')->name('banners.create');
//        Route::post('store','SuperAdmin\BannerController@store')->name('banners.store');
//        Route::get('edit/{id}','SuperAdmin\BannerController@edit')->name('banners.edit');
//        Route::post('update/{id}','SuperAdmin\BannerController@update')->name('banners.update');
//        Route::get('delete/{id}','SuperAdmin\BannerController@delete')->name('banners.delete');
//    });
//
//    Route::group(['prefix'=>'category'], function(){
//        Route::get('/','SuperAdmin\CategoryController@index')->name('category.list');
//        Route::get('create','SuperAdmin\CategoryController@create')->name('category.create');
//        Route::post('store','SuperAdmin\CategoryController@store')->name('category.store');
//        Route::get('edit/{id}','SuperAdmin\CategoryController@edit')->name('category.edit');
//        Route::post('update/{id}','SuperAdmin\CategoryController@update')->name('category.update');
//
//    });
//
//    Route::group(['prefix'=>'subcategory'], function(){
//        Route::get('/','SuperAdmin\SubCategoryController@index')->name('subcategory.list');
//        Route::get('create','SuperAdmin\SubCategoryController@create')->name('subcategory.create');
//        Route::post('store','SuperAdmin\SubCategoryController@store')->name('subcategory.store');
//        Route::get('edit/{id}','SuperAdmin\SubCategoryController@edit')->name('subcategory.edit');
//        Route::post('update/{id}','SuperAdmin\SubCategoryController@update')->name('subcategory.update');
//
//    });
//
//    Route::group(['prefix'=>'product'], function(){
//        Route::get('/','SuperAdmin\ProductController@index')->name('product.list');
//        Route::get('create','SuperAdmin\ProductController@create')->name('product.create');
//        Route::post('store','SuperAdmin\ProductController@store')->name('product.store');
//        Route::get('edit/{id}','SuperAdmin\ProductController@edit')->name('product.edit');
//        Route::post('update/{id}','SuperAdmin\ProductController@update')->name('product.update');
//        Route::post('product-sizeprice/{id}','SuperAdmin\ProductController@sizeprice')->name('product.sizeprice');
//        Route::post('size-update','SuperAdmin\ProductController@updatesizeprice')->name('product.size.update');
//        Route::get('size-images','SuperAdmin\ProductController@allimages')->name('product.size.images');
//
//        Route::post('product-category-create/{id}','SuperAdmin\ProductController@productcategory')->name('product.category.create');
//        Route::get('delete/{id}','SuperAdmin\ProductController@delete')->name('product.delete');
//
//        Route::post('document/{id}','SuperAdmin\ProductController@document')->name('product.document');
//
//        Route::get('bulk-upload','SuperAdmin\ProductController@bulk_upload_form')->name('product.bulk.form');
//        Route::post('bulk-upload','SuperAdmin\ProductController@bulk_upload')->name('product.bulk.upload');
//
//
//    });
//    Route::group(['prefix'=>'homesection'], function(){
//        Route::get('/','SuperAdmin\HomeSectionController@index')->name('homesection.list');
//        Route::get('banner-create','SuperAdmin\HomeSectionController@bannercreate')->name('homesection.bannercreate');
//        Route::post('banner-store','SuperAdmin\HomeSectionController@bannerstore')->name('homesection.bannerstore');
//        Route::get('banner-edit/{id}','SuperAdmin\HomeSectionController@banneredit')->name('homesection.banneredit');
//        Route::post('banner-update/{id}','SuperAdmin\HomeSectionController@bannerupdate')->name('homesection.bannerupdate');
//        Route::get('productcreate','SuperAdmin\HomeSectionController@productcreate')->name('homesection.productcreate');
//        Route::post('productstore','SuperAdmin\HomeSectionController@productstore')->name('homesection.productstore');
//        Route::get('productedit/{id}','SuperAdmin\HomeSectionController@productedit')->name('homesection.productedit');
//        Route::post('productupdate/{id}','SuperAdmin\HomeSectionController@productupdate')->name('homesection.productupdate');
//        Route::post('productimage/{id}','SuperAdmin\HomeSectionController@productImage')->name('homesection.productimage');
//        Route::get('productdelete/{id}','SuperAdmin\HomeSectionController@productdelete')->name('homesection.productdelete');
//        Route::get('sub-category-create','SuperAdmin\HomeSectionController@subcategorycreate')->name('homesection.subcategorycreate');
//        Route::post('sub-category-store','SuperAdmin\HomeSectionController@subcategorystore')->name('homesection.subcategorystore');
//        Route::get('sub-category-edit/{id}','SuperAdmin\HomeSectionController@subcategoryedit')->name('homesection.subcategoryedit');
//        Route::post('sub-category-update/{id}','SuperAdmin\HomeSectionController@subcategoryupdate')->name('homesection.subcategoryupdate');
//        Route::post('subcategoryimage/{id}','SuperAdmin\HomeSectionController@subcategoryimage')->name('homesection.subcategoryimage');
//        Route::get('subdelete/{id}','SuperAdmin\HomeSectionController@subdelete')->name('homesection.subdelete');
//        Route::get('home-section-delete/{id}','SuperAdmin\HomeSectionController@homesectiondelete')->name('homesection.homesectiondelete');
//
//    });
//
//    Route::group(['prefix'=>'coupon'], function(){
//        Route::get('/','SuperAdmin\CouponController@index')->name('coupon.list');
//        Route::get('create','SuperAdmin\CouponController@create')->name('coupon.create');
//        Route::post('store','SuperAdmin\CouponController@store')->name('coupon.store');
//        Route::get('edit/{id}','SuperAdmin\CouponController@edit')->name('coupon.edit');
//        Route::post('update/{id}','SuperAdmin\CouponController@update')->name('coupon.update');
//
//    });
//
//    Route::group(['prefix'=>'orders'], function(){
//        Route::get('/','SuperAdmin\OrderController@index')->name('orders.list');
//        Route::get('details/{id}','SuperAdmin\OrderController@details')->name('order.details');
//        //Route::get('product','SuperAdmin\OrderController@product')->name('orders.product');
//        Route::get('change-status/{id}','SuperAdmin\OrderController@changeStatus')->name('order.status.change');
//        Route::get('change-payment-status/{id}','SuperAdmin\OrderController@changePaymentStatus')->name('payment.status.change');
//        Route::post('changeRider/{id}','SuperAdmin\OrderController@changeRider')->name('rider.change');
//        Route::get('add-cashback/{id}/{type}','SuperAdmin\OrderController@addCashback')->name('add.cashback');
//
//    });
//
//    Route::group(['prefix'=>'returnproduct'], function(){
//        Route::get('/','SuperAdmin\ReturnProductController@index')->name('return.product.list');
//
//    });
//
//    Route::group(['prefix'=>'sales'], function(){
//        Route::get('/','SuperAdmin\SalesController@index')->name('sales.list');
//
//    });
//
//
//    Route::group(['prefix'=>'inventory'], function(){
//        Route::get('packets','SuperAdmin\InventoryController@packet')->name('packets.list');
//        Route::get('quantity','SuperAdmin\InventoryController@quantity')->name('quantity.list');
//
//    });
//
//
//    Route::group(['prefix'=>'timeslot'], function(){
//        Route::get('/','SuperAdmin\TimeSlotController@index')->name('timeslot.list');
//        Route::get('create','SuperAdmin\TimeSlotController@create')->name('timeslot.create');
//        Route::post('store','SuperAdmin\TimeSlotController@store')->name('timeslot.store');
//        Route::get('edit/{id}','SuperAdmin\TimeSlotController@edit')->name('timeslot.edit');
//        Route::post('update/{id}','SuperAdmin\TimeSlotController@update')->name('timeslot.update');
//
//    });
//
//    Route::group(['prefix'=>'area'], function(){
//        Route::get('/','SuperAdmin\AreaController@index')->name('area.list');
//        Route::get('create','SuperAdmin\AreaController@create')->name('area.create');
//        Route::post('store','SuperAdmin\AreaController@store')->name('area.store');
//        Route::get('edit/{id}','SuperAdmin\AreaController@edit')->name('area.edit');
//        Route::post('update/{id}','SuperAdmin\AreaController@update')->name('area.update');
//        Route::post('import','SuperAdmin\AreaController@import')->name('area.import');
//
//    });
//
//    Route::group(['prefix'=>'rider'], function(){
//        Route::get('/','SuperAdmin\RiderController@index')->name('rider.list');
//        Route::get('create','SuperAdmin\RiderController@create')->name('rider.create');
//        Route::post('store','SuperAdmin\RiderController@store')->name('rider.store');
//        Route::get('edit/{id}','SuperAdmin\RiderController@edit')->name('rider.edit');
//        Route::post('update/{id}','SuperAdmin\RiderController@update')->name('rider.update');
//
//    });
//
//    Route::group(['prefix'=>'stores'], function(){
//        Route::get('/','SuperAdmin\StoreController@index')->name('stores.list');
//        Route::get('create','SuperAdmin\StoreController@create')->name('stores.create');
//        Route::post('store','SuperAdmin\StoreController@store')->name('stores.store');
//        Route::get('edit/{id}','SuperAdmin\StoreController@edit')->name('stores.edit');
//        Route::post('update/{id}','SuperAdmin\StoreController@update')->name('stores.update');
//
//    });
//   //****************************************end*************************************************
//
//
//    Route::group(['prefix'=>'customer'], function(){
//        Route::get('/','SuperAdmin\CustomerController@index')->name('customer.list');
//        Route::get('edit/{id}','SuperAdmin\CustomerController@edit')->name('customer.edit');
//        Route::post('update/{id}','SuperAdmin\CustomerController@update')->name('customer.update');
//        Route::post('send_message','SuperAdmin\CustomerController@send_message')->name('customer.send_message');
//    });
//
//    Route::group(['prefix'=>'complain'], function(){
//        Route::get('/','SuperAdmin\ComplainController@index')->name('complain.list');
//        Route::get('view/{id}','SuperAdmin\ComplainController@details')->name('complain.view');
//        Route::post('message','SuperAdmin\ComplainController@send_message')->name('complain.message');
//        Route::get('mark-closed/{id}','SuperAdmin\ComplainController@markAsClosed')->name('complain.close');
//
//    });
//
//    Route::group(['prefix'=>'news'], function(){
//        Route::get('/','SuperAdmin\NewsUpdateController@index')->name('news.list');
//        Route::get('create','SuperAdmin\NewsUpdateController@create')->name('news.create');
//        Route::post('store','SuperAdmin\NewsUpdateController@store')->name('news.store');
//        Route::get('edit/{id}','SuperAdmin\NewsUpdateController@edit')->name('news.edit');
//        Route::post('update/{id}','SuperAdmin\NewsUpdateController@update')->name('news.update');
//
//    });
//
//    Route::group(['prefix'=>'notification'], function(){
//        Route::get('create','SuperAdmin\NotificationController@create')->name('notification.create');
//        Route::post('store','SuperAdmin\NotificationController@store')->name('notification.store');
//
//    });
//
//    Route::group(['prefix'=>'wallet'], function(){
//
//        Route::post('add-remove-wallet-balance', 'SuperAdmin\WalletController@addremove')->name('wallet.add.remove');
//
//        Route::get('get-wallet-balance/{id}', 'SuperAdmin\WalletController@getbalance')->name('user.wallet.balance');
//
//    });
//
//    Route::group(['prefix'=>'reports'], function(){
//
//        Route::get('sales-report', 'SuperAdmin\ReportDownloader@downloadSalesReport')->name('sales.report');
//
//        Route::get('order-report', 'SuperAdmin\ReportDownloader@downloadOrderReport')->name('order.report');
//
//    });
//
//    Route::group(['prefix'=>'subadmin'], function(){
//
//        Route::group(['can'=>'view.subadmin'], function() {
//            Route::get('subadmins', 'SuperAdmin\SubAdminController@index')->name('subadmin.list');
//
//            Route::get('create', 'SuperAdmin\SubAdminController@create')->name('subadmin.create');
//
//            Route::get('edit/{id}', 'SuperAdmin\SubAdminController@edit')->name('subadmin.edit');
//        });
//        Route::group(['can'=>'update.subadmin'], function(){
//            Route::post('create', 'SuperAdmin\SubAdminController@store');
//
//            Route::post('edit/{id}', 'SuperAdmin\SubAdminController@update');
//        });
//
//    });
//
//
//});

Route::group(['prefix'=>'api'], function() {
//    Route::get('privacy-policy', 'SuperAdmin\PolicyController@index')->name('policy.view');
//    Route::get('terms-condition', 'SuperAdmin\PolicyController@terms')->name('terms.view');
//    Route::get('about-us', 'SuperAdmin\PolicyController@about')->name('about.view');
    Route::get('invoice/{id}', 'SuperAdmin\PolicyController@invoice')->name('invoice.view');
});


//Route::group(['prefix'=>'store-admin', 'middleware'=>['auth', 'acl'], 'is'=>'store'], function() {
//
//    Route::get('/dashboard', 'StoreAdmin\DashboardController@index')->name('storeadmin.home');
//
//
//    Route::group(['prefix'=>'orders'], function(){
//        Route::get('/','StoreAdmin\OrderController@index')->name('storeadmin.orders.list');
//        Route::get('details/{id}','StoreAdmin\OrderController@details')->name('storeadmin.order.details');
//
//        Route::get('change-status/{id}','SuperAdmin\OrderController@changeStatus')->name('storeadmin.order.status.change');
//
//        Route::post('changeRider/{id}','SuperAdmin\OrderController@changeRider')->name('storeadmin.rider.change');
//
//    });
//
//    Route::group(['prefix'=>'rider'], function() {
//        Route::get('/', 'StoreAdmin\RiderController@index')->name('storeadmin.rider.list');
//        Route::get('create', 'StoreAdmin\RiderController@create')->name('storeadmin.rider.create');
//        Route::post('store', 'StoreAdmin\RiderController@store')->name('storeadmin.rider.store');
//        Route::get('edit/{id}', 'StoreAdmin\RiderController@edit')->name('storeadmin.rider.edit');
//        Route::post('update/{id}', 'StoreAdmin\RiderController@update')->name('storeadmin.rider.update');
//    });
//
//    Route::group(['prefix'=>'returnproduct'], function(){
//        Route::get('/','StoreAdmin\ReturnProductController@index')->name('storeadmin.return.product.list');
//
//    });
//
//
//});

Route::group(['middleware'=>['auth', 'acl']], function(){

    Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
    Route::group(['is'=>'admin|dashboard-viewer'], function(){
        Route::get('/dashboard', 'SuperAdmin\DashboardController@index')->name('home');
    });

    Route::group(['is'=>'subadmin'], function() {
        Route::get('/subadmin-dashboard', 'SuperAdmin\SubAdminDashboardController@index')->name('subadmin.home');
    });

//****************************************House Goods********************************************

    Route::group(['prefix' => 'configurations'], function () {
        Route::group(['is'=>'admin|configuration-viewer'], function() {
            Route::get('/', 'SuperAdmin\ConfigurationController@index')->name('configurations.list');
        });
        Route::group(['is'=>'admin|configuration-editor'], function() {
            Route::post('/', 'SuperAdmin\ConfigurationController@update');
        });
    });


    Route::group(['prefix'=>'banners'], function(){

        Route::group(['is'=>'admin|banner-viewer'], function(){
            Route::get('/','SuperAdmin\BannerController@index')->name('banners.list');
            Route::get('create','SuperAdmin\BannerController@create')->name('banners.create');
            Route::get('edit/{id}','SuperAdmin\BannerController@edit')->name('banners.edit');
        });
        Route::group(['is'=>'admin|banner-editor'], function() {
            Route::post('store', 'SuperAdmin\BannerController@store')->name('banners.store');

            Route::post('update/{id}', 'SuperAdmin\BannerController@update')->name('banners.update');
            Route::get('delete/{id}', 'SuperAdmin\BannerController@delete')->name('banners.delete');
        });
    });

    Route::group(['prefix'=>'category'], function(){
        Route::group(['is'=>'admin|category-viewer'], function(){
            Route::get('/','SuperAdmin\CategoryController@index')->name('category.list');
            Route::get('create','SuperAdmin\CategoryController@create')->name('category.create');
            Route::get('edit/{id}','SuperAdmin\CategoryController@edit')->name('category.edit');
        });
        Route::group(['is'=>'admin|category-editor'], function(){
            Route::post('store','SuperAdmin\CategoryController@store')->name('category.store');

            Route::post('update/{id}','SuperAdmin\CategoryController@update')->name('category.update');
        });
    });

    Route::group(['prefix'=>'subcategory'], function(){
        Route::group(['is'=>'admin|subcategory-viewer'], function(){
            Route::get('/','SuperAdmin\SubCategoryController@index')->name('subcategory.list');
            Route::get('create','SuperAdmin\SubCategoryController@create')->name('subcategory.create');
            Route::get('edit/{id}','SuperAdmin\SubCategoryController@edit')->name('subcategory.edit');
        });
        Route::group(['is'=>'admin|subcategory-editor'], function(){
            Route::post('store','SuperAdmin\SubCategoryController@store')->name('subcategory.store');

            Route::post('update/{id}','SuperAdmin\SubCategoryController@update')->name('subcategory.update');
        });

    });

    Route::group(['prefix'=>'product'], function(){
        Route::group(['is'=>'admin|product-viewer'], function(){
            Route::get('/','SuperAdmin\ProductController@index')->name('product.list');
            Route::get('create','SuperAdmin\ProductController@create')->name('product.create');
            Route::get('edit/{id}','SuperAdmin\ProductController@edit')->name('product.edit');
            Route::get('size-images','SuperAdmin\ProductController@allimages')->name('product.size.images');
            Route::get('bulk-upload','SuperAdmin\ProductController@bulk_upload_form')->name('product.bulk.form');
        });
        Route::group(['is'=>'admin|product-editor'], function(){
            Route::post('store','SuperAdmin\ProductController@store')->name('product.store');

            Route::post('update/{id}','SuperAdmin\ProductController@update')->name('product.update');
            Route::post('product-sizeprice/{id}','SuperAdmin\ProductController@sizeprice')->name('product.sizeprice');
            Route::post('size-update','SuperAdmin\ProductController@updatesizeprice')->name('product.size.update');
            Route::get('delete/{id}','SuperAdmin\ProductController@delete')->name('product.delete');
            Route::post('product-category-create/{id}','SuperAdmin\ProductController@productcategory')->name('product.category.create');


            Route::post('document/{id}','SuperAdmin\ProductController@document')->name('product.document');
            Route::post('bulk-upload','SuperAdmin\ProductController@bulk_upload')->name('product.bulk.upload');
        });






    });
    Route::group(['prefix'=>'homesection'], function(){
        Route::group(['is'=>'admin|homesection-viewer'], function(){
            Route::get('/','SuperAdmin\HomeSectionController@index')->name('homesection.list');
            Route::get('banner-create','SuperAdmin\HomeSectionController@bannercreate')->name('homesection.bannercreate');
            Route::get('banner-edit/{id}','SuperAdmin\HomeSectionController@banneredit')->name('homesection.banneredit');
            Route::get('productcreate','SuperAdmin\HomeSectionController@productcreate')->name('homesection.productcreate');
            Route::get('productedit/{id}','SuperAdmin\HomeSectionController@productedit')->name('homesection.productedit');
            Route::get('productdelete/{id}','SuperAdmin\HomeSectionController@productdelete')->name('homesection.productdelete');
            Route::get('sub-category-create','SuperAdmin\HomeSectionController@subcategorycreate')->name('homesection.subcategorycreate');
            Route::get('sub-category-edit/{id}','SuperAdmin\HomeSectionController@subcategoryedit')->name('homesection.subcategoryedit');
        });
        Route::group(['is'=>'admin|homesection-editor'], function(){
            Route::post('banner-store','SuperAdmin\HomeSectionController@bannerstore')->name('homesection.bannerstore');

            Route::post('banner-update/{id}','SuperAdmin\HomeSectionController@bannerupdate')->name('homesection.bannerupdate');
            Route::post('productstore','SuperAdmin\HomeSectionController@productstore')->name('homesection.productstore');
            Route::post('productupdate/{id}','SuperAdmin\HomeSectionController@productupdate')->name('homesection.productupdate');
            Route::post('productimage/{id}','SuperAdmin\HomeSectionController@productImage')->name('homesection.productimage');
            Route::post('sub-category-store','SuperAdmin\HomeSectionController@subcategorystore')->name('homesection.subcategorystore');
            Route::post('sub-category-update/{id}','SuperAdmin\HomeSectionController@subcategoryupdate')->name('homesection.subcategoryupdate');
            Route::post('subcategoryimage/{id}','SuperAdmin\HomeSectionController@subcategoryimage')->name('homesection.subcategoryimage');
            Route::get('subdelete/{id}','SuperAdmin\HomeSectionController@subdelete')->name('homesection.subdelete');
            Route::get('home-section-delete/{id}','SuperAdmin\HomeSectionController@homesectiondelete')->name('homesection.homesectiondelete');
        });





    });

    Route::group(['prefix'=>'coupon'], function(){
        Route::group(['is'=>'admin|coupon-viewer'], function(){
            Route::get('/','SuperAdmin\CouponController@index')->name('coupon.list');
            Route::get('create','SuperAdmin\CouponController@create')->name('coupon.create');
            Route::get('edit/{id}','SuperAdmin\CouponController@edit')->name('coupon.edit');
        });

        Route::group(['is'=>'admin|coupon-editor'], function(){
            Route::post('store','SuperAdmin\CouponController@store')->name('coupon.store');
            Route::post('update/{id}','SuperAdmin\CouponController@update')->name('coupon.update');
        });

    });

    Route::group(['prefix'=>'orders'], function(){
        Route::group(['is'=>'admin|order-viewer'], function(){
            Route::get('/','SuperAdmin\OrderController@index')->name('orders.list');
            Route::get('details/{id}','SuperAdmin\OrderController@details')->name('order.details');
            Route::post('invoice-update/{id}','SuperAdmin\OrderController@invoice_update')->name('order.invoice.update');
        });
        Route::group(['is'=>'admin|order-editor'], function(){
            Route::get('change-status/{id}','SuperAdmin\OrderController@changeStatus')->name('order.status.change');
            Route::get('change-payment-status/{id}','SuperAdmin\OrderController@changePaymentStatus')->name('payment.status.change');
            Route::post('changeRider/{id}','SuperAdmin\OrderController@changeRider')->name('rider.change');
            Route::get('add-cashback/{id}/{type}','SuperAdmin\OrderController@addCashback')->name('add.cashback');
        });

        //Route::get('product','SuperAdmin\OrderController@product')->name('orders.product');

    });

    Route::group(['prefix'=>'returnproduct'], function(){
        Route::group(['is'=>'admin|return-viewer'], function(){
            Route::get('/','SuperAdmin\ReturnProductController@index')->name('return.product.list');
            Route::get('return-export{id}','SuperAdmin\ReturnProductController@export')->name('return.return.export');
        });
    });

    Route::group(['prefix'=>'sales'], function(){
        Route::group(['is'=>'admin|sales-viewer'], function(){
            Route::get('/','SuperAdmin\SalesController@index')->name('sales.list');
        });
    });


    Route::group(['prefix'=>'inventory'], function(){
        Route::group(['is'=>'admin|inventory-viewer'], function(){
            Route::get('packets','SuperAdmin\InventoryController@packet')->name('packets.list');
            Route::get('quantity','SuperAdmin\InventoryController@quantity')->name('quantity.list');
        });
    });


    Route::group(['prefix'=>'returnrequest'], function(){
        Route::group(['is'=>'admin|returnrequest-viewer'], function() {
            Route::get('/', 'SuperAdmin\ReturnRequestController@index')->name('returnrequest.list');
        });

        Route::group(['is'=>'admin|returnrequest-editor'], function() {
            Route::get('approve-request/{return_id}','SuperAdmin\ReturnRequestController@approveReturnProduct')->name('approve.return.request');

            Route::post('cancel-request/{return_id}','SuperAdmin\ReturnRequestController@cancelReturnRequest')->name('cancel.return.request');
        });
    });


    Route::group(['prefix'=>'timeslot'], function(){
        Route::group(['is'=>'admin|returnrequest-viewer'], function() {
            Route::get('/', 'SuperAdmin\TimeSlotController@index')->name('timeslot.list');
        });

        Route::group(['is'=>'admin|returnrequest-editor'], function() {
            Route::get('create', 'SuperAdmin\TimeSlotController@create')->name('timeslot.create');
            Route::post('store', 'SuperAdmin\TimeSlotController@store')->name('timeslot.store');
            Route::get('edit/{id}', 'SuperAdmin\TimeSlotController@edit')->name('timeslot.edit');
            Route::post('update/{id}', 'SuperAdmin\TimeSlotController@update')->name('timeslot.update');
        });

    });

    Route::group(['prefix'=>'area'], function(){
        Route::group(['is'=>'admin|arealist-viewer'], function(){
            Route::get('/','SuperAdmin\AreaController@index')->name('area.list');
            Route::get('create','SuperAdmin\AreaController@create')->name('area.create');
            Route::get('edit/{id}','SuperAdmin\AreaController@edit')->name('area.edit');

        });
        Route::group(['is'=>'admin|arealist-editor'], function(){
            Route::post('store','SuperAdmin\AreaController@store')->name('area.store');
            Route::post('update/{id}','SuperAdmin\AreaController@update')->name('area.update');
            Route::post('import','SuperAdmin\AreaController@import')->name('area.import');
            Route::get('export','SuperAdmin\AreaController@export')->name('area.export');

        });

    });

    Route::group(['prefix'=>'rider'], function(){
        Route::group(['is'=>'admin|rider-viewer'], function(){
            Route::get('/','SuperAdmin\RiderController@index')->name('rider.list');
            Route::get('create','SuperAdmin\RiderController@create')->name('rider.create');
            Route::get('edit/{id}','SuperAdmin\RiderController@edit')->name('rider.edit');

        });
        Route::group(['is'=>'admin|rider-editor'], function(){
            Route::post('store','SuperAdmin\RiderController@store')->name('rider.store');
            Route::post('update/{id}','SuperAdmin\RiderController@update')->name('rider.update');

        });

    });

    Route::group(['prefix'=>'stores'], function(){
        Route::group(['is'=>'admin|store-viewer'], function(){
            Route::get('/','SuperAdmin\StoreController@index')->name('stores.list');
            Route::get('create','SuperAdmin\StoreController@create')->name('stores.create');
            Route::get('edit/{id}','SuperAdmin\StoreController@edit')->name('stores.edit');

        });
        Route::group(['is'=>'admin|store-editor'], function(){
            Route::post('store','SuperAdmin\StoreController@store')->name('stores.store');
            Route::post('update/{id}','SuperAdmin\StoreController@update')->name('stores.update');

        });
    });
    //****************************************end**


    Route::group(['prefix'=>'customer'], function(){
        Route::group(['is'=>'admin|customer-viewer'], function(){
            Route::get('/','SuperAdmin\CustomerController@index')->name('customer.list');
            Route::get('edit/{id}','SuperAdmin\CustomerController@edit')->name('customer.edit');
        });
        Route::group(['is'=>'admin|customer-editor'], function(){

            Route::post('update/{id}','SuperAdmin\CustomerController@update')->name('customer.update');
            Route::post('send_message','SuperAdmin\CustomerController@send_message')->name('customer.send_message');
        });

    });

    Route::group(['prefix'=>'complain'], function(){
        Route::group(['is'=>'admin|complaint-viewer'], function(){
            Route::get('/','SuperAdmin\ComplainController@index')->name('complain.list');
            Route::get('view/{id}','SuperAdmin\ComplainController@details')->name('complain.view');
            Route::post('message','SuperAdmin\ComplainController@send_message')->name('complain.message');

        });
        Route::group(['is'=>'admin|complaint-editor'], function(){
            Route::get('mark-closed/{id}','SuperAdmin\ComplainController@markAsClosed')->name('complain.close');

        });

    });

    Route::group(['prefix'=>'news'], function(){

        Route::get('/','SuperAdmin\NewsUpdateController@index')->name('news.list');
        Route::get('create','SuperAdmin\NewsUpdateController@create')->name('news.create');
        Route::post('store','SuperAdmin\NewsUpdateController@store')->name('news.store');
        Route::get('edit/{id}','SuperAdmin\NewsUpdateController@edit')->name('news.edit');
        Route::post('update/{id}','SuperAdmin\NewsUpdateController@update')->name('news.update');

    });

    Route::group(['prefix'=>'membership'], function(){

        Route::get('/','SuperAdmin\MembershipController@index')->name('membership.list');
        Route::get('create','SuperAdmin\MembershipController@create')->name('membership.create');
        Route::post('store','SuperAdmin\MembershipController@store')->name('membership.store');
        Route::get('edit/{id}','SuperAdmin\MembershipController@edit')->name('membership.edit');
        Route::post('update/{id}','SuperAdmin\MembershipController@update')->name('membership.update');

    });

    Route::group(['prefix'=>'purchase'], function(){
        Route::group(['is'=>'admin|purchase-viewer'], function() {
            Route::get('/', 'SuperAdmin\PurchaseController@index')->name('purchase.list');
            Route::get('create', 'SuperAdmin\PurchaseController@create')->name('purchase.create');
            Route::get('export{id}','SuperAdmin\PurchaseController@export')->name('purchase.export');
        });
        Route::group(['is'=>'admin|purchase-editor'], function() {
            Route::post('store', 'SuperAdmin\PurchaseController@store')->name('purchase.store');
        });

    });

    Route::group(['prefix'=>'notification'], function(){
        Route::group(['is'=>'admin|notification-editor'], function(){
            Route::get('create','SuperAdmin\NotificationController@create')->name('notification.create');
            Route::post('store','SuperAdmin\NotificationController@store')->name('notification.store');

        });

    });

    Route::group(['prefix'=>'wallet'], function(){

        Route::post('add-remove-wallet-balance', 'SuperAdmin\WalletController@addremove')->name('wallet.add.remove');

        Route::get('get-wallet-balance/{id}', 'SuperAdmin\WalletController@getbalance')->name('user.wallet.balance');

        Route::get('get-wallet-history/{id}', 'SuperAdmin\WalletController@getWalletHistory')->name('user.wallet.history');

    });

    Route::group(['prefix'=>'reports'], function(){

        Route::get('sales-report', 'SuperAdmin\ReportDownloader@downloadSalesReport')->name('sales.report');

        Route::get('order-report', 'SuperAdmin\ReportDownloader@downloadOrderReport')->name('order.report');

    });

    Route::group(['prefix'=>'subadmin'], function(){

        Route::group(['is'=>'admin|subadmin-viewer'], function() {
            Route::get('subadmins', 'SuperAdmin\SubAdminController@index')->name('subadmin.list');

            Route::get('create', 'SuperAdmin\SubAdminController@create')->name('subadmin.create');

            Route::get('edit/{id}', 'SuperAdmin\SubAdminController@edit')->name('subadmin.edit');
        });
        Route::group(['is'=>'admin|subadmin-editor'], function(){
            Route::post('create', 'SuperAdmin\SubAdminController@store');

            Route::post('edit/{id}', 'SuperAdmin\SubAdminController@update');
        });

    });

});

Route::group(['prefix'=>'url'], function() {
    Route::get('privacy-policy', 'SuperAdmin\AppUrlController@privacypolicy')->name('policy');
    Route::get('terms-condition', 'SuperAdmin\AppUrlController@termscondition')->name('terms');
    Route::get('about-us', 'SuperAdmin\AppUrlController@aboutus')->name('about');
    Route::get('cancellation-policy', 'SuperAdmin\AppUrlController@cancelationpolicy')->name('cancelation');
    Route::get('refund-policy', 'SuperAdmin\AppUrlController@refundpolicy')->name('contact');
});

