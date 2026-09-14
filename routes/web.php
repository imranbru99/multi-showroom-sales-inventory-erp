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

// Route::get('/', function () {
//     return view('welcome');
// });
//admin panel start here
// helper
// insert vendor from previous purchase table
Route::get('/insert-vendor-from-previous', 'DBScriptController@insertVendorFromPreviousPurchase');

// insert vendor id to previous purchase from vendor setup

Route::get('/b', 'DBScriptController@b');

// insert cash customer id in previous sales
Route::get('/c', 'DBScriptController@c');

// insert non-cash customer info from previous sales
Route::get('/d', 'DBScriptController@d');


// insert customer id to previous sales
Route::get('/e', 'DBScriptController@e');


// insert product from prev_purchase to product table
Route::get('/f', 'DBScriptController@f');

// insert product_id from product table to previous purchase
Route::get('/g', 'DBScriptController@g');

// fix date in previous purchase
Route::get('/h', 'DBScriptController@h');

// fix date in previous purchase
Route::get('/i', 'DBScriptController@i');

// insert staff from previous sale to staff table
Route::get('/j', 'DBScriptController@j');

// insert staff id in previous sale table
Route::get('/k', 'DBScriptController@k');

// insert cash retail sales from previous table.
Route::get('/l', 'DBScriptController@l');

// insert installment retail sales from previous table.
Route::get('/m', 'DBScriptController@m');

// get unique products
Route::get('/n', 'DBScriptController@n');

// add vendor Id in previous lifting returns
Route::get('/o', 'DBScriptController@o');

// add product Id in previous lifting returns
Route::get('/p', 'DBScriptController@p');

// add product from previous lifting returns where product id not found
Route::get('/q', 'DBScriptController@q');

// Insert lifting return from previous return
Route::get('/r', 'DBScriptController@r');

// fix date in previous sale
Route::get('/s', 'DBScriptController@s');

// insert account no where blank
Route::get('/u', 'DBScriptController@u');

// insert customer where only collection without sale.
Route::get('/v', 'DBScriptController@v');

// insert collection from previous collection
Route::get('/w', 'DBScriptController@w');

// temp 1
Route::get('/x', 'DBScriptController@x');

// temp 2
Route::get('/y', 'DBScriptController@y');






Auth::routes();

Route::get('/index', 'HomeController@index')->name('admin.index');
Route::get('/', 'HomeController@showroom')->name('admin.showroom');

Route::prefix('admin')->group(function () {
    Route::middleware('auth:admin')->group(function () {
        Route::group(['middleware' => 'menuPermission'], function () {
            //Dashboard Link url
            Route::get('/index', 'HomeController@index')->name('admin.index');
            Route::get('/', 'HomeController@showroom')->name('admin.showroom');
            Route::post('/', 'HomeController@loginWithShowroom')->name('admin.loginWithShowroom');

            // User Management Start
            // Settings
            Route::get('/website-information', 'Admin\SettingsController@information')->name('site.info');
            Route::post('/update-information', 'Admin\SettingsController@updatSettings')->name('settings.update');
            Route::get('/admin-logo', 'Admin\SettingsController@adminLogo')->name('admin.logo');
            Route::post('/adminLogo-update', 'Admin\SettingsController@updatadminLogo')->name('adminLogo.update');

            //User Menu
            Route::get('/user-menu', 'Admin\UserMenuController@index')->name('usermenu.index');
            Route::get('/user-menu/add', 'Admin\UserMenuController@add')->name('usermenu.add');
            Route::post('/user-menu/save', 'Admin\UserMenuController@save')->name('usermenu.save');
            Route::get('/user-menu/edit/{id}', 'Admin\UserMenuController@edit')->name('usermenu.edit');
            Route::post('/user-menu/update', 'Admin\UserMenuController@update')->name('usermenu.update');
            Route::get('/user-menu/status', 'Admin\UserMenuController@status')->name('usermenu.status');
            Route::post('/usermenu-delete', 'Admin\UserMenuController@destroy')->name('usermenu-delete');

            //User Menu link action
            Route::get('/user-menu-link/{id}', 'Admin\UserMenuController@usermenuLink')->name('usermenuLink.index');
            Route::get('/user-menu-link-add/{menuId}', 'Admin\UserMenuController@usermenuLinkAdd')->name('userMenu.ActionLinkAdd');
            Route::post('/user-menu-link-save/{parentMenuId}', 'Admin\UserMenuController@usermenuLinkSave')->name('userMenu.ActionLinkSave');
            Route::get('/user-menu-link-edit/{menuId}/{id}', 'Admin\UserMenuController@usermenuLinkEdit')->name('userMenu.ActionLinkEdit');
            Route::post('/user-menu-link-update/{parentMenuId}', 'Admin\UserMenuController@usermenuLinkUpdate')->name('userMenu.ActionLinkUpdate');
            Route::get('/user-menu-action/status', 'Admin\UserMenuController@actionStatus')->name('usermenuAction.status');
            Route::post('/user-menu-action/delete', 'Admin\UserMenuController@actionDestroy')->name('usermenuAction.delete');

            //User Manage
            Route::get('/user', 'Admin\AdminController@index')->name('user.index');
            Route::get('/user-add', 'Admin\AdminController@addUser')->name('user.add');
            Route::post('/user-save', 'Admin\AdminController@saveUser')->name('user.save');
            Route::get('/user-edit/{id}', 'Admin\AdminController@editUser')->name('user.edit');
            Route::post('/user-upate', 'Admin\AdminController@updateUser')->name('user.update');
            Route::get('/user-change-password/{id}', 'Admin\AdminController@password')->name('user.changePassword');
            Route::post('/user-save-password', 'Admin\AdminController@passwordChange')->name('user.savePassword');
            Route::post('/user-profile', 'Admin\AdminController@userProfile')->name('user.profile');
            Route::post('/user-delete', 'Admin\AdminController@deleteUser')->name('user.delete');
            Route::post('/user-status', 'Admin\AdminController@changeUserStatus')->name('user.status');


            //User List
            Route::get('user-list', 'Admin\UserListController@index')->name('userList.index');
            Route::post('user-list', 'Admin\UserListController@index')->name('userList.index');
            Route::post('user-list/print', 'Admin\UserListController@print')->name('userList.print');


            //User Pass Change
            Route::get('change-password/{id}', 'Admin\AdminController@password')->name('user.changePass');
            Route::post('save-password', 'Admin\AdminController@passwordChange')->name('user.savePass');

            //User Roll Manage
            Route::get('/user-roles', 'Admin\UserRoleController@index')->name('user-roles.index');
            Route::get('/user-role-add', 'Admin\UserRoleController@adduserRole')->name('userRoleAdd.page');
            Route::post('/user-role-save', 'Admin\UserRoleController@saveuserRole')->name('userRole.save');
            Route::get('/user-role-edit/{id}', 'Admin\UserRoleController@edituserRole')->name('userRole.edit');
            Route::post('/user-role-upate', 'Admin\UserRoleController@updateuserRole')->name('userRole.update');
            Route::post('/user-role-delete', 'Admin\UserRoleController@deleteUserRole')->name('userRole.delete');
            Route::get('/userRole/status/{id}', 'Admin\UserRoleController@changeuserRoleStatus')->name('userRole.changeuserRoleStatus');
            Route::get('/user-role-permission/{id}', 'Admin\UserRoleController@permission')->name('userRole.permission');
            Route::post('/user-role-permission-update', 'Admin\UserRoleController@permissionUpdate')->name('userRole.permissionUpdate');

            Route::post('get-menus', 'Admin\UserRoleController@loadData')->name('userRole.loadMenu');

            // Company Setup
            Route::get('/company-setup', 'Admin\CompanySetupController@index')->name('companySetup.index');
            Route::get('/company-setup-add', 'Admin\CompanySetupController@add')->name('companySetup.add');
            Route::post('/company-setup-save', 'Admin\CompanySetupController@save')->name('companySetup.save');
            Route::get('/company-setup-edit/{id}', 'Admin\CompanySetupController@edit')->name('companySetup.edit');
            Route::post('/company-setup-update', 'Admin\CompanySetupController@update')->name('companySetup.update');
            Route::post('/company-setup-status', 'Admin\CompanySetupController@changeStatus')->name('companySetup.status');


            //Showroom Setup
            Route::get('/showroom-setup', 'Admin\ShowroomSetupController@index')->name('showroomSetup.index');
            Route::get('/showroom-setup-add', 'Admin\ShowroomSetupController@add')->name('showroomSetup.add');
            Route::post('/showroom-setup-save', 'Admin\ShowroomSetupController@save')->name('showroomSetup.save');
            Route::get('/showroom-setup-edit/{id}', 'Admin\ShowroomSetupController@edit')->name('showroomSetup.edit');
            Route::post('/showroom-setup-update', 'Admin\ShowroomSetupController@update')->name('showroomSetup.update');
            Route::post('/showroom-setup-delete', 'Admin\ShowroomSetupController@delete')->name('showroomSetup.delete');
            Route::post('/showroom-setup', 'Admin\ShowroomSetupController@changeStatus')->name('showroomSetup.status');
            // User Management End
            // Bussiness Settings
            //Category Setup
            Route::get('/category-setup', 'Admin\CategorySetupController@index')->name('categorySetup.index');
            Route::get('/category-setup-add', 'Admin\CategorySetupController@add')->name('categorySetup.add');
            Route::post('/category-setup-save', 'Admin\CategorySetupController@save')->name('categorySetup.save');
            Route::get('/category-setup-edit/{id}', 'Admin\CategorySetupController@edit')->name('categorySetup.edit');
            Route::post('/category-setup-update', 'Admin\CategorySetupController@update')->name('categorySetup.update');
            Route::post('/category-setup-delete', 'Admin\CategorySetupController@delete')->name('categorySetup.delete');
            Route::post('/category-setup-status', 'Admin\CategorySetupController@changeStatus')->name('categorySetup.status');

            //Product Section
            Route::get('/product-setup', 'Admin\ProductSetupController@index')->name('productSetup.index');

            Route::get('/product-setup-add', 'Admin\ProductSetupController@addProduct')->name('productSetup.add');
            Route::post('/product-setup-basic-info-save', 'Admin\ProductSetupController@saveProductBasicInfo')->name('productSetupBasicInfo.save');
            Route::post('/product-setup-image-save', 'Admin\ProductSetupController@saveProductImage')->name('productSetupImage.save');

            Route::get('/product-setup-edit/{id}', 'Admin\ProductSetupController@editProduct')->name('productSetup.edit');
            Route::post('/product-setup-basic-info-update', 'Admin\ProductSetupController@updateProductBasicInfo')->name('productSetupBasicInfo.update');
            Route::post('/product-setup-advance-info-update', 'Admin\ProductSetupController@updateProductAdvanceInfo')->name('productSetupAdvanceInfo.update');
            Route::post('/product-setup-seo-info-update', 'Admin\ProductSetupController@updateProductSeoInfo')->name('productSetupSeoInfo.update');

            Route::post('/product-setup-image-delete', 'Admin\ProductSetupController@deleteProductImage')->name('productSetupImage.delete');

            Route::post('/product-setup-delete', 'Admin\ProductSetupController@deleteProduct')->name('productSetup.delete');
            Route::post('/products-setup-status', 'Admin\ProductSetupController@changeProductStatus')->name('productSetup.status');


            Route::post('type-products', 'Admin\ProductSetupController@productType')->name('product.type');




            // Showroom Project Setup
            Route::get('/showroomProject-setup', 'Admin\ShowroomProjectController@index')->name('showroomProjectSetup.index');
            Route::get('/showroomProject-setup-add', 'Admin\ShowroomProjectController@add')->name('showroomProjectSetup.add');
            Route::post('/showroomProject-setup-save', 'Admin\ShowroomProjectController@save')->name('showroomProjectSetup.save');
            Route::get('/showroomProject-setup-edit/{id}', 'Admin\ShowroomProjectController@edit')->name('showroomProjectSetup.edit');
            Route::post('/showroomProject-setup-update', 'Admin\ShowroomProjectController@update')->name('showroomProjectSetup.update');
            Route::post('/showroomProject-setup-delete', 'Admin\ShowroomProjectController@delete')->name('showroomProjectSetup.delete');
            Route::post('/showroomProject-setup-status', 'Admin\ShowroomProjectController@changeStatus')->name('showroomProjectSetup.status');


            // Store Setup
            Route::get('/store-setup', 'Admin\StoreSetupController@index')->name('storeSetup.index');
            Route::get('/store-setup-add', 'Admin\StoreSetupController@add')->name('storeSetup.add');
            Route::post('/store-setup-save', 'Admin\StoreSetupController@save')->name('storeSetup.save');
            Route::get('/store-setup-edit/{id}', 'Admin\StoreSetupController@edit')->name('storeSetup.edit');
            Route::post('/store-setup-update', 'Admin\StoreSetupController@update')->name('storeSetup.update');
            Route::post('/store-setup-delete', 'Admin\StoreSetupController@delete')->name('storeSetup.delete');
            Route::post('/store-setup-status', 'Admin\StoreSetupController@changeStatus')->name('storeSetup.status');

            // Bank Setup
            Route::get('/bank-setup', 'Admin\BankSetupController@index')->name('bankSetup.index');
            Route::get('/bank-setup-add', 'Admin\BankSetupController@add')->name('bankSetup.add');
            Route::post('/bank-setup-save', 'Admin\BankSetupController@save')->name('bankSetup.save');
            Route::get('/bank-setup-edit/{id}', 'Admin\BankSetupController@edit')->name('bankSetup.edit');
            Route::post('/bank-setup-update', 'Admin\BankSetupController@update')->name('bankSetup.update');
            Route::post('/bank-setup-delete', 'Admin\BankSetupController@delete')->name('bankSetup.delete');
            Route::post('/bank-setup-status', 'Admin\BankSetupController@changeStatus')->name('bankSetup.status');

            // Courier Setup
            Route::get('/courier-setup', 'Admin\CourierSetupController@index')->name('courierSetup.index');
            Route::get('/courier-setup-add', 'Admin\CourierSetupController@add')->name('courierSetup.add');
            Route::post('/courier-setup-save', 'Admin\CourierSetupController@save')->name('courierSetup.save');
            Route::get('/courier-setup-edit/{id}', 'Admin\CourierSetupController@edit')->name('courierSetup.edit');
            Route::post('/courier-setup-update', 'Admin\CourierSetupController@update')->name('courierSetup.update');
            Route::post('/courier-setup-delete', 'Admin\CourierSetupController@delete')->name('courierSetup.delete');
            Route::post('/courier-setup-status', 'Admin\CourierSetupController@changeStatus')->name('courierSetup.status');

            // Vehicle Setup
            Route::get('/vehicle-setup', 'Admin\VehicleSetupController@index')->name('vehicleSetup.index');
            Route::get('/vehicle-setup-add', 'Admin\VehicleSetupController@add')->name('vehicleSetup.add');
            Route::post('/vehicle-setup-save', 'Admin\VehicleSetupController@save')->name('vehicleSetup.save');
            Route::get('/vehicle-setup-edit/{id}', 'Admin\VehicleSetupController@edit')->name('vehicleSetup.edit');
            Route::post('/vehicle-setup-update', 'Admin\VehicleSetupController@update')->name('vehicleSetup.update');
            Route::post('/vehicle-setup-delete', 'Admin\VehicleSetupController@delete')->name('vehicleSetup.delete');
            Route::post('/vehicle-setup-status', 'Admin\VehicleSetupController@changeStatus')->name('vehicleSetup.status');

            // Area Setup
            Route::get('/area-setup', 'Admin\AreaSetupController@index')->name('areaSetup.index');
            Route::get('/area-setup-add', 'Admin\AreaSetupController@add')->name('areaSetup.add');
            Route::post('/area-setup-save', 'Admin\AreaSetupController@save')->name('areaSetup.save');
            Route::get('/area-setup-edit/{id}', 'Admin\AreaSetupController@edit')->name('areaSetup.edit');
            Route::post('/area-setup-update', 'Admin\AreaSetupController@update')->name('areaSetup.update');
            Route::post('/area-setup-delete', 'Admin\AreaSetupController@delete')->name('areaSetup.delete');
            Route::post('/area-setup-status', 'Admin\AreaSetupController@changeStatus')->name('areaSetup.status');

            // Region Setup
            Route::get('/region-setup', 'Admin\RegionSetupController@index')->name('regionSetup.index');
            Route::get('/region-setup-add', 'Admin\RegionSetupController@add')->name('regionSetup.add');
            Route::post('/region-setup-save', 'Admin\RegionSetupController@save')->name('regionSetup.save');
            Route::get('/region-setup-edit/{id}', 'Admin\RegionSetupController@edit')->name('regionSetup.edit');
            Route::post('/region-setup-update', 'Admin\RegionSetupController@update')->name('regionSetup.update');
            Route::post('/region-setup-delete', 'Admin\RegionSetupController@delete')->name('regionSetup.delete');
            Route::post('/region-setup-status', 'Admin\RegionSetupController@changeStatus')->name('regionSetup.status');

            // Territory Setup
            Route::get('/territory-setup', 'Admin\TerritorySetupController@index')->name('territorySetup.index');
            Route::get('/territory-setup-add', 'Admin\TerritorySetupController@add')->name('territorySetup.add');
            Route::post('/territory-setup-save', 'Admin\TerritorySetupController@save')->name('territorySetup.save');
            Route::get('/territory-setup-edit/{id}', 'Admin\TerritorySetupController@edit')->name('territorySetup.edit');
            Route::post('/territory-setup-update', 'Admin\TerritorySetupController@update')->name('territorySetup.update');
            Route::post('/territory-setup-delete', 'Admin\TerritorySetupController@delete')->name('territorySetup.delete');
            Route::post('/territory-setup-status', 'Admin\TerritorySetupController@changeStatus')->name('territorySetup.status');

            // Staff Setup
            Route::get('/staff-setup', 'Admin\StaffSetupController@index')->name('staffSetup.index');
            Route::get('/staff-setup-add', 'Admin\StaffSetupController@add')->name('staffSetup.add');
            Route::post('/staff-setup-save', 'Admin\StaffSetupController@save')->name('staffSetup.save');
            Route::get('/staff-setup-edit/{id}', 'Admin\StaffSetupController@edit')->name('staffSetup.edit');
            Route::post('/staff-setup-update', 'Admin\StaffSetupController@update')->name('staffSetup.update');
            Route::post('/staff-setup-delete', 'Admin\StaffSetupController@delete')->name('staffSetup.delete');
            Route::post('/staff-setup-status', 'Admin\StaffSetupController@changeStatus')->name('staffSetup.status');
            Route::get('/staff-setup/print', 'Admin\StaffSetupController@print')->name('staffSetup.print');


            //Staff List
            Route::get('staff-list', 'Admin\StaffListController@index')->name('staffList.index');
            Route::post('staff-list', 'Admin\StaffListController@index')->name('staffList.index');
            Route::post('staff-list/print', 'Admin\StaffListController@print')->name('staffList.print');



            // Vendor Setup
            Route::get('/vendor-setup', 'Admin\VendorSetupController@index')->name('vendorSetup.index');
            Route::get('/vendor-setup-add', 'Admin\VendorSetupController@add')->name('vendorSetup.add');
            Route::post('/vendor-setup-save', 'Admin\VendorSetupController@save')->name('vendorSetup.save');
            Route::get('/vendor-setup-edit/{id}', 'Admin\VendorSetupController@edit')->name('vendorSetup.edit');
            Route::post('/vendor-setup-update', 'Admin\VendorSetupController@update')->name('vendorSetup.update');
            Route::post('/vendor-setup-delete', 'Admin\VendorSetupController@delete')->name('vendorSetup.delete');
            Route::post('/vendor-setup-status', 'Admin\VendorSetupController@changeStatus')->name('vendorSetup.status');

            //Group Setup
            Route::get('/group-setup', 'Admin\GroupSetupController@index')->name('groupSetup.index');
            Route::get('/group-setup-add', 'Admin\GroupSetupController@add')->name('groupSetup.add');
            Route::post('/group-setup-save', 'Admin\GroupSetupController@save')->name('groupSetup.save');
            Route::get('/group-setup-edit/{id}', 'Admin\GroupSetupController@edit')->name('groupSetup.edit');
            Route::post('/group-setup-update', 'Admin\GroupSetupController@update')->name('groupSetup.update');
            Route::post('/group-setup-delete', 'Admin\GroupSetupController@delete')->name('groupSetup.delete');
            Route::post('/group-setup-status', 'Admin\GroupSetupController@changeStatus')->name('groupSetup.status');
            Route::post('/group-setup-status/staff-list', 'Admin\GroupSetupController@getAllStaff')->name('groupSetup.getAllStaff');

            //Product List
            Route::get('/product-list', 'Admin\ProductListController@index')->name('productList.index');
            Route::post('/product-list', 'Admin\ProductListController@index')->name('productList.index');
            Route::post('/product-list/print', 'Admin\ProductListController@print')->name('productList.print');

            //Back Part Print
            Route::get('/back-part', 'Admin\BackPartController@index')->name('backPart.index');
            Route::post('/back-part', 'Admin\BackPartController@index')->name('backPart.index');


            // Business Settings End
            // Product Lifting Start
            // Lifting
            Route::get('/lifting', 'Admin\LiftingController@index')->name('lifting.index');
            Route::get('/lifting-add', 'Admin\LiftingController@add')->name('lifting.add');
            Route::post('/lifting-save', 'Admin\LiftingController@save')->name('lifting.save');
            Route::get('/lifting-edit/{id}', 'Admin\LiftingController@edit')->name('lifting.edit');
            Route::post('/lifting-update', 'Admin\LiftingController@update')->name('lifting.update');
            Route::post('/lifting-delete', 'Admin\LiftingController@delete')->name('lifting.delete');
            Route::post('/lifting-product-info', 'Admin\LiftingController@liftingProductInfo')->name('lifting.productInfo');
            Route::get('/lifting-print/{id}', 'Admin\LiftingController@print')->name('lifting.print');
            Route::get('/lifting-print-barcode/{id}', 'Admin\LiftingController@printBarCode')->name('lifting.print.barcode');

            Route::post('lifting-product-type', 'Admin\LiftingController@variantProduct')->name('lifting.variantProduct');




            // Route::post('/barcode/print', 'Admin\LiftingController@barCodePrint')->name('barcode.print');
            // Lifting Return
            Route::get('/lifting-return', 'Admin\LiftingReturnController@index')->name('liftingReturn.index');
            Route::get('/lifting-return-add', 'Admin\LiftingReturnController@add')->name('liftingReturn.add');
            Route::post('/lifting-return-save', 'Admin\LiftingReturnController@save')->name('liftingReturn.save');
            Route::get('/lifting-return-edit/{id}', 'Admin\LiftingReturnController@edit')->name('liftingReturn.edit');
            Route::post('/lifting-return-update', 'Admin\LiftingReturnController@update')->name('liftingReturn.update');
            Route::post('/lifting-return-delete', 'Admin\LiftingReturnController@delete')->name('liftingReturn.delete');
            Route::post('/lifting-return-product-info', 'Admin\LiftingReturnController@liftingProductInfo')->name('liftingReturn.liftingProductInfo');
            Route::get('/lifting-return-print/{id}', 'Admin\LiftingReturnController@print')->name('liftingReturn.print');

            Route::post('/lifting-return-product-type', 'Admin\LiftingReturnController@variant')->name('liftingReturn.variant');
            Route::post('lifting-return-voucher-product', 'Admin\LiftingReturnController@liftingNoProducts')->name('liftingReturn.liftingNoProducts');



            // Payment to Company
            Route::get('/payment-to-company', 'Admin\PaymentToCompanyController@index')->name('paymentToCompany.index');
            Route::get('/payment-to-company/add', 'Admin\PaymentToCompanyController@add')->name('paymentToCompany.add');
            Route::post('/payment-to-company/save', 'Admin\PaymentToCompanyController@save')->name('paymentToCompany.save');
            Route::get('/payment-to-company/edit/{id}', 'Admin\PaymentToCompanyController@edit')->name('paymentToCompany.edit');
            Route::post('/payment-to-company/update', 'Admin\PaymentToCompanyController@update')->name('paymentToCompany.update');
            Route::post('/payment-to-company/delete', 'Admin\PaymentToCompanyController@delete')->name('paymentToCompany.delete');
            Route::post('/get-vendor-info', 'Admin\PaymentToCompanyController@getVendorInfo')->name('getVendorInfo');

            // Payment Record
            Route::get('/payment-record', 'Admin\PaymentRecordController@index')->name('paymentRecord.index');
            Route::post('/payment-record', 'Admin\PaymentRecordController@index')->name('paymentRecord.index');
            Route::post('/payment-record/print', 'Admin\PaymentRecordController@print')->name('paymentRecord.print');

            // Lifting Record
            Route::get('/lifting-record', 'Admin\LiftingRecordController@index')->name('liftingRecord.index');
            Route::post('/lifting-record', 'Admin\LiftingRecordController@index')->name('liftingRecord.index');
            Route::post('/lifting-record/print', 'Admin\LiftingRecordController@print')->name('liftingRecord.print');

            // Lifting Return Record
            Route::get('/lifting-return-record', 'Admin\LiftingReturnRecordController@index')->name('liftingReturnRecord.index');
            Route::post('/lifting-return-record', 'Admin\LiftingReturnRecordController@index')->name('liftingReturnRecord.index');
            Route::post('/lifting-return-record/print', 'Admin\LiftingReturnRecordController@print')->name('liftingReturnRecord.print');

            //Lifting & payment Summery
            Route::get('/lifting-payment-summary', 'Admin\LiftingPaymentSummaryController@index')->name('liftingPaymentSummary.index');
            Route::post('/lifting-payment-summary', 'Admin\LiftingPaymentSummaryController@index')->name('liftingPaymentSummary.index');
            Route::post('/lifting-payment-summary/print', 'Admin\LiftingPaymentSummaryController@print')->name('liftingPaymentSummary.print');

            //Vendor Statement
            Route::get('/vendor-statement', 'Admin\VendorStatementController@index')->name('vendorStatement.index');
            Route::post('/vendor-statement', 'Admin\VendorStatementController@index')->name('vendorStatement.index');
            Route::post('/vendor-statement/print', 'Admin\VendorStatementController@print')->name('vendorStatement.print');

            // Product Lifting End
            // Start Inventory Management
            //Out Of Stock Report
            Route::get('/out-of-stock', 'Admin\OutOfStockController@index')->name('outOfStock.index');
            Route::post('/out-of-stock', 'Admin\OutOfStockController@index')->name('outOfStock.index');
            Route::post('/out-of-stock/print', 'Admin\OutOfStockController@print')->name('outOfStock.print');

            //Out Of Stock Report By Value
            Route::get('/out-of-stock-by-value', 'Admin\OutOfStockValueController@index')->name('outOfStockValue.index');
            Route::post('/out-of-stock-by-value', 'Admin\OutOfStockValueController@index')->name('outOfStockValue.index');
            Route::post('/out-of-stock-by-value/print', 'Admin\OutOfStockValueController@print')->name('outOfStockValue.print');

            //Stock Valuation Report
            Route::get('/stock-valuation', 'Admin\StockValuationController@index')->name('stockValuation.index');
            Route::post('/stock-valuation', 'Admin\StockValuationController@index')->name('stockValuation.index');
            Route::post('/stock-valuation/print', 'Admin\StockValuationController@print')->name('stockValuation.print');

            //Stock Status Report
            Route::get('/stock-status', 'Admin\StockStatusController@index')->name('stockStatus.index');
            Route::post('/stock-status', 'Admin\StockStatusController@index')->name('stockStatus.index');
            Route::post('/stock-status/print', 'Admin\StockStatusController@print')->name('stockStatus.print');

            // Product Transfer
            Route::get('/transfer-product', 'Admin\TransferProductController@index')->name('transferProduct.index');
            Route::get('/transfer-product-add', 'Admin\TransferProductController@add')->name('transferProduct.add');
            Route::post('/transfer-product-save', 'Admin\TransferProductController@save')->name('transferProduct.save');
            Route::get('/transfer-product-edit/{id}', 'Admin\TransferProductController@edit')->name('transferProduct.edit');
            Route::post('transfer-product-update', 'Admin\TransferProductController@update')->name('transferProduct.update');
            Route::post('/transfer-product-delete', 'Admin\TransferProductController@delete')->name('transferProduct.delete');
            Route::post('/transfer-product/store-showroom-list', 'Admin\TransferProductController@storeAndShowroomInfo')->name('transferProduct.storeAndShowroomInfo');
            Route::get('/transfer-product/product-info', 'Admin\TransferProductController@liftingProductInfo')->name('transferProduct.liftingProductInfo');
            Route::get('/transfer-product-print/{id}', 'Admin\TransferProductController@print')->name('transferProduct.print');
            Route::post('/transfer-product-info', 'Admin\TransferProductController@getSerialProduct')->name('transferProduct.getSerialProduct');
            Route::get('transfer-consumer-product-info', 'Admin\TransferProductController@getConsumerProduct')->name('transferProduct.getConsumerProduct');



            // Product Receive
            Route::get('receive-product', 'Admin\ReceiveProductController@index')->name('receiveProduct.index');
            Route::get('receive-product-approve/{id}', 'Admin\ReceiveProductController@approve')->name('receiveProduct.approve');
            Route::post('receive-product-save', 'Admin\ReceiveProductController@save')->name('receiveProduct.save');
            Route::get('receive-product-print/{id}', 'Admin\ReceiveProductController@print')->name('receiveProduct.print');


            // Transfer Report
            Route::get('/transport-report', 'Admin\TransferReportController@index')->name('transferReport.index');
            Route::post('/transport-report', 'Admin\TransferReportController@index')->name('transferReport.index');
            Route::post('/transport-report/print', 'Admin\TransferReportController@print')->name('transferReport.print');


            // Transfer Receive Report
            Route::get('/transfer-receive-report', 'Admin\TransferReceiveReportController@index')->name('transferReceiveReport.index');
            Route::post('/transfer-receive-report', 'Admin\TransferReceiveReportController@index')->name('transferReceiveReport.index');
            Route::post('/transfer-receive-report/print', 'Admin\TransferReceiveReportController@print')->name('transferReceiveReport.print');

            //Product Statement
            Route::get('/product-statement', 'Admin\ProductStatementController@index')->name('productStatement.index');
            Route::post('/product-statement', 'Admin\ProductStatementController@index')->name('productStatement.index');
            Route::post('/product-statement/print', 'Admin\ProductStatementController@print')->name('productStatement.print');

            // End Inventory Management
            // Customer Registration
            Route::get('/customerRegistration', 'Admin\CustomerRegistrationController@index')->name('customerRegistration.index');
            Route::get('/customerRegistration-add', 'Admin\CustomerRegistrationController@add')->name('customerRegistration.add');
            Route::post('/customerRegistration-save', 'Admin\CustomerRegistrationController@save')->name('customerRegistration.save');
            Route::get('/customerRegistration-edit/{id}', 'Admin\CustomerRegistrationController@edit')->name('customerRegistration.edit');
            Route::post('/customerRegistration-update', 'Admin\CustomerRegistrationController@update')->name('customerRegistration.update');
            Route::post('/customerRegistration-status', 'Admin\CustomerRegistrationController@status')->name('customerRegistration.status');
            Route::post('/customerRegistration-delete', 'Admin\CustomerRegistrationController@delete')->name('customerRegistration.delete');
            Route::get('/customerRegistration-view/{id}', 'Admin\CustomerRegistrationController@view')->name('customerRegistration.view');
            Route::get('/customerRegistration-print/{id}', 'Admin\CustomerRegistrationController@print')->name('customerRegistration.print');
            Route::get('/customerRegistration-print-all', 'Admin\CustomerRegistrationController@printAll')->name('customerRegistration.print.all');

            // Retails Sales
            Route::get('/retailSales', 'Admin\RetailSalesController@index')->name('retailSales.index');
            Route::get('/retailSales-add', 'Admin\RetailSalesController@add')->name('retailSales.add');
            Route::post('/retailSales-save', 'Admin\RetailSalesController@save')->name('retailSales.save');
            Route::get('/retailSales-edit/{id}', 'Admin\RetailSalesController@edit')->name('retailSales.edit');
            Route::post('/retailSales-update', 'Admin\RetailSalesController@update')->name('retailSales.update');
            Route::post('/retailSales-status', 'Admin\RetailSalesController@status')->name('retailSales.status');
            Route::post('/retailSales-delete', 'Admin\RetailSalesController@delete')->name('retailSales.delete');
            Route::get('/retailSales-view/{id}', 'Admin\RetailSalesController@view')->name('retailSales.view');
            Route::get('/retailSales-print/{id}', 'Admin\RetailSalesController@print')->name('retailSales.print');
            Route::post('/retailSales-print/get-products', 'Admin\RetailSalesController@getAllProduct')->name('retailSales.getAllProduct');
            Route::post('/retailSales-print/get-customer-info', 'Admin\RetailSalesController@getCustomerInfo')->name('retailSales.getCustomerInfo');
            Route::post('product-info', 'Admin\RetailSalesController@getProductInfo')->name('retailSales.getProductInfo');


            // retail Sales return
            Route::get('/retail-sales-return', 'Admin\RetailSalesReturnController@index')->name('retailSales.return.index');
            Route::get('/retail-sales-return-add', 'Admin\RetailSalesReturnController@add')->name('retailSales.return.add');
            Route::get('/retail-sales-return-customer-products', 'Admin\RetailSalesReturnController@getCustomerProducts')->name('retailSales.return.customerProducts');
            Route::post('/retail-sales-return-save', 'Admin\RetailSalesReturnController@save')->name('retailSales.return.save');
            Route::get('/retail-sales-return-edit/{id}', 'Admin\RetailSalesReturnController@edit')->name('retailSales.return.edit');
            Route::post('/retail-sales-return-update', 'Admin\RetailSalesReturnController@update')->name('retailSales.return.update');
            Route::post('/retail-sales-return-delete', 'Admin\RetailSalesReturnController@delete')->name('retailSales.return.delete');



            // Retails Sales Offer
            Route::get('/offer', 'Admin\OfferController@index')->name('offer.index');
            Route::get('/add-offer', 'Admin\OfferController@add')->name('offer.add');
            Route::post('/save-offer', 'Admin\OfferController@save')->name('offer.save');
            Route::get('/edit-offer/{id}', 'Admin\OfferController@edit')->name('offer.edit');
            Route::post('/update-offer', 'Admin\OfferController@update')->name('offer.update');
            Route::post('/offer-status', 'Admin\OfferController@changeStatus')->name('offer.status');
            Route::post('/delete-offer', 'Admin\OfferController@delete')->name('offer.delete');


            // Start Sales Management
            //Customer registration setup developed
            Route::get('/customer-registration-setup', 'Admin\CustomerRegistrationSetupController@index')->name('customerRegistraionSetup.index');
            Route::post('/customer-registration-setup', 'Admin\CustomerRegistrationSetupController@index')->name('customerRegistraionSetup.index');

            Route::get('/customer-registration-setup-add', 'Admin\CustomerRegistrationSetupController@add')->name('customerRegistraionSetup.add');
            Route::post('/customer-registration-setup-save', 'Admin\CustomerRegistrationSetupController@save')->name('customerRegistraionSetup.save');

            Route::get('/customer-registration-setup-view/{id}', 'Admin\CustomerRegistrationSetupController@viewCustomerDetails')->name('customerRegistraionSetup.view');
            Route::post('/customer-registration-setup-new-product-save', 'Admin\CustomerRegistrationSetupController@newProductSave')->name('customerRegistraionSetup.NewProductSave');

            Route::post('/customer-registration-setup-delete', 'Admin\CustomerRegistrationSetupController@delete')->name('customerRegistraionSetup.delete');
            Route::post('/customer-registration/get-product-info', 'Admin\CustomerRegistrationSetupController@getProductInfo')->name('customerRegistration.getProductInfo');

            Route::post('/customer-registration/get-guarantor-info', 'Admin\CustomerRegistrationSetupController@getGuarantorInfo')->name('customerRegistration.getGuarantorInfo');

            Route::get('/customer-registration-setup-edit/{id}', 'Admin\CustomerRegistrationSetupController@edit')->name('customerRegistraionSetup.editCustomer');
            Route::post('/customer-registration-setup-update', 'Admin\CustomerRegistrationSetupController@update')->name('customerRegistraionSetup.update');

            Route::get('/customer-registration-setup/{customerId}/product-edit/{productId}/', 'Admin\CustomerRegistrationSetupController@editCustomerProduct')->name('customerRegistraionSetup.editCustomerProduct');
            Route::post('/customer-registration-setup/update-customer-product', 'Admin\CustomerRegistrationSetupController@editCustomerProduct')->name('customerRegistraionSetup.updateCustomerProduct');

            Route::get('/customer-registration-setup/{customerId}/guarantor-edit/{guarantorId}/', 'Admin\CustomerRegistrationSetupController@editGuarantor')->name('customerRegistraionSetup.editGuarantor');
            Route::post('/customer-registration-setup/update-customer-guarantor', 'Admin\CustomerRegistrationSetupController@editGuarantor')->name('customerRegistraionSetup.updateCustomerGuarantor');

            Route::get('/customer-registration-setup/print/{customerId}', 'Admin\CustomerRegistrationSetupController@print')->name('customerRegistraionSetup.print');

            Route::post('/customer-registration-setup/print', 'Admin\CustomerRegistrationSetupController@customerListPrint')->name('customerRegistraionSetup.customerListPrint');

            //Invoice setup developed by Jisan
            Route::get('/invoice-setup', 'Admin\InvoiceSetupController@index')->name('invoiceSetup.index');
            Route::get('/invoice-setup-add', 'Admin\InvoiceSetupController@add')->name('invoiceSetup.add');
            Route::post('/invoice-setup-save', 'Admin\InvoiceSetupController@save')->name('invoiceSetup.save');
            Route::get('/invoice-setup-view/{id}', 'Admin\InvoiceSetupController@view')->name('invoiceSetup.view');
            Route::get('/invoice-setup-print-invoice/{invoiceId}', 'Admin\InvoiceSetupController@printInvoice')->name('invoiceSetup.printInvoice');
            Route::get('/invoice-setup-print-chalan/{invoiceId}', 'Admin\InvoiceSetupController@printChalan')->name('invoiceSetup.printChalan');
            Route::post('/invoice-setup-delete', 'Admin\InvoiceSetupController@delete')->name('invoiceSetup.delete');
            Route::post('/invoice-setup/product-list', 'Admin\InvoiceSetupController@getAllProduct')->name('invoiceSetup.getAllProduct');
            Route::post('/invoice-setup/product-serial-list', 'Admin\InvoiceSetupController@getAllProductSerial')->name('invoiceSetup.getAllProductSerial');

            //Cash Collection developed by Jisan
            Route::get('/cash-collection', 'Admin\CashCollectionController@index')->name('cashCollection.index');

            Route::get('/cash-collection-add', 'Admin\CashCollectionController@add')->name('cashCollection.add');
            Route::post('/cash-collection-save', 'Admin\CashCollectionController@save')->name('cashCollection.save');
            Route::post('/cash-collection-get-invoice-information', 'Admin\CashCollectionController@getInvoiceInformation')->name('cashCollection.getInvoiceInformation');

            Route::get('/cash-collection-edit/{id}', 'Admin\CashCollectionController@edit')->name('cashCollection.edit');
            Route::post('/cash-collection-update', 'Admin\CashCollectionController@update')->name('cashCollection.update');

            Route::get('/cash-collection-print/{collectionId}', 'Admin\CashCollectionController@print')->name('cashCollection.print');

            Route::post('/cash-collection-delete', 'Admin\CashCollectionController@delete')->name('cashCollection.delete');

            // Retail Sale Collection
            Route::get('/retail-collection', "Admin\RetailCollectionController@index")->name('retailCollection.index');
            Route::get('/retail-collection-add', "Admin\RetailCollectionController@add")->name('retailCollection.add');
            Route::post('/retail-collection-save', "Admin\RetailCollectionController@save")->name('retailCollection.save');
            Route::get('/retail-collection-edit/{id}', "Admin\RetailCollectionController@edit")->name('retailCollection.edit');
            Route::post('/retail-collection-update', "Admin\RetailCollectionController@update")->name('retailCollection.update');
            Route::post('/retail-collection-delete', 'Admin\RetailCollectionController@delete')->name('retail.collection.delete');
            //            Route::post('/retail-collection-ByCollector', "Admin\RetailCollectionController@addByCollector")->name('retailCollectionByCollector.add');
            Route::post('/retail-collection-cash-account', "Admin\RetailCollectionController@addCashAccount")->name('retailCollectionByCollector.addCashAccount');
            Route::post('/retail-collection-ByAccounNo', "Admin\RetailCollectionController@addAccountInfo")->name('retailCollectionByCollector.addAccount');


            //Group Sales Target Setup
            Route::get('/group-sales-target-setup', 'Admin\GroupSalesTargetSetupController@index')->name('groupSalesTargetSetup.index');
            Route::get('/group-sales-target-setup-add', 'Admin\GroupSalesTargetSetupController@add')->name('groupSalesTargetSetup.add');
            Route::post('/group-sales-target-setup-save', 'Admin\GroupSalesTargetSetupController@save')->name('groupSalesTargetSetup.save');
            Route::get('/group-sales-target-setup-edit/{id}', 'Admin\GroupSalesTargetSetupController@edit')->name('groupSalesTargetSetup.edit');
            Route::post('/group-sales-target-setup-update', 'Admin\GroupSalesTargetSetupController@update')->name('groupSalesTargetSetup.update');
            Route::post('/group-sales-target-setup-delete', 'Admin\GroupSalesTargetSetupController@delete')->name('groupSalesTargetSetup.delete');

            // Start Customer Outstanding Report
            Route::any('/customer-outstanding', 'Admin\CustomerOutstandingController@index')->name('customerOutstanding.index');
            Route::post('/customer-outstanding/print', 'Admin\CustomerOutstandingController@print')->name('customerOutstanding.print');
            Route::get('/customer-outstanding-customers', 'Admin\CustomerOutstandingController@getCustomers')->name('customerOutstanding.getCustomers');

            Route::any('/customer-savings', 'Admin\CustomerOutstandingController@CustomerSavingsIndex')->name('customerSavings.index');
            Route::post('/customer-savings/print', 'Admin\CustomerOutstandingController@CustomerSavingsPrint')->name('customerSavings.print');
            Route::get('/customer-savings-customers', 'Admin\CustomerOutstandingController@getSavingsCustomers')->name('customerSavings.getSavingsCustomers');

            // UpComing Collection List
            Route::any('/up-collection-list', 'Admin\CustomerOutstandingController@UpCollectionList')->name('up.collection.index');
            Route::post('/up-collection-list/print', 'Admin\CustomerOutstandingController@UpCollectionListPrint')->name('up.collection.print');

            // Over Due Schedule List
            Route::any('over-due-report', 'Admin\OverDueScheduleController@index')->name('overDueSchedule.index');
            Route::post('over-due-report/print', 'Admin\OverDueScheduleController@print')->name('overDueSchedule.print');

            // Invoice Duration List
            Route::any('/invoice-duration', 'Admin\InvoiceDurationReport@index')->name('invoice.duration.index');
            Route::post('/invoice-duration/print', 'Admin\InvoiceDurationReport@print')->name('invoice.duration.print');


            // Close Account List
            Route::any('close-account-report', 'Admin\CloseAccountReportController@index')->name('closeAccountReport.index');
            Route::post('close-account-report/print', 'Admin\CloseAccountReportController@print')->name('closeAccountReport.print');


            // Missing Amount Report
            Route::any('missing-amount-report', 'Admin\MissingAmountReportController@index')->name('missingAmountReport.index');
            Route::post('missing-amount-report/print', 'Admin\MissingAmountReportController@print')->name('missingAmountReport.print');


            // Running Account List
            Route::any('running-account', 'Admin\RunningAccountController@index')->name('runningAccount.index');
            Route::post('running-account/print', 'Admin\RunningAccountController@print')->name('runningAccount.print');


            // Overdue List
            Route::any('overdue-report', 'Admin\OverDueReportController@index')->name('overDueReport.index');
            Route::post('overdue-report/print', 'Admin\OverDueReportController@print')->name('overDueReport.print');

            // Overdue List
            Route::any('overdue-month-report', 'Admin\OverDueMonthReportController@index')->name('overDueMonthReport.index');
            Route::post('overdue-month-report/print', 'Admin\OverDueMonthReportController@print')->name('overDueMonthReport.print');

            // UpComing Collection List
            Route::get('/reschedule-installment', 'Admin\ReScheduleInstallmentController@index')->name('reschedule.installment');
            Route::post('/reschedule-installment-save-single', 'Admin\ReScheduleInstallmentController@UpDateSingle')->name('reschedule.installment.save');
            Route::get('/reschedule-auto', 'Admin\ReScheduleInstallmentController@rescheduleAuto')->name('reschedule.installment.auto');
            Route::post('/reschedule-auto-save', 'Admin\ReScheduleInstallmentController@rescheduleAutoSave')->name('reschedule.installment.auto.save');
            Route::get('reschedule-customers', 'Admin\ReScheduleInstallmentController@getCustomers')->name('reschedule.getCustomers');


            // Start Customer Statement Report
            Route::get('/customer-statement', 'Admin\CustomerStatementController@index')->name('customerStatement.index');
            Route::post('/customer-statement', 'Admin\CustomerStatementController@index')->name('customerStatement.index');
            Route::post('/customer-statement/print', 'Admin\CustomerStatementController@print')->name('customerStatement.print');
            Route::get('customer-statement-customers', 'Admin\CustomerStatementController@getCustomers')->name('customerStatement.getCustomers');

            // Start Commission Configuration
            Route::get('/commission-configuration', 'Admin\CommmissionConfigurationController@index')->name('commissionConfiguration.index');
            Route::get('/commission-configuration-add', 'Admin\CommmissionConfigurationController@add')->name('commissionConfiguration.add');
            Route::post('/commission-configuration-save', 'Admin\CommmissionConfigurationController@save')->name('commissionConfiguration.save');

            Route::get('/commission-configuration-view/{id}', 'Admin\CommmissionConfigurationController@view')->name('commissionConfiguration.view');
            Route::get('/commission-configuration-print/{id}', 'Admin\CommmissionConfigurationController@print')->name('commissionConfiguration.print');
            Route::post('/commission-configuration-info', 'Admin\CommmissionConfigurationController@getInfo')->name('commissionConfiguration.info');

            // Sales History
            Route::get('/sales-history', 'Admin\SalesHistoryController@index')->name('salesHistory.index');
            Route::post('/sales-history', 'Admin\SalesHistoryController@index')->name('salesHistory.index');
            Route::post('/sales-history/print', 'Admin\SalesHistoryController@print')->name('salesHistory.print');

            // Sales Return History
            Route::get('/retail-sales-return-history', 'Admin\SalesHistoryController@indexReturn')->name('RetailsalesReturnHistory.index');
            Route::post('/retail-sales-return-history', 'Admin\SalesHistoryController@indexReturn')->name('RetailsalesReturnHistory.index');
            Route::post('/retail-sales-return-history/print', 'Admin\SalesHistoryController@printReturn')->name('RetailsalesReturnHistory.print');
            Route::get('/retail-sales-return-history-customers', 'Admin\SalesHistoryController@getCustomers')->name('RetailsalesReturnHistory.getCustomers');


            // Sales Details Report
            route::get('/sales-details-report', 'Admin\SalesHistoryController@salesDetails')->name('sales.details.report.page');
            route::post('/sales-details-report', 'Admin\SalesHistoryController@salesDetails')->name('sales.details.report.page');

            //Cash Collection History List
            Route::get('/cash-collection-history', 'Admin\CashCollectionHistoryController@index')->name('cashCollectionHistory.index');
            Route::post('/cash-collection-history', 'Admin\CashCollectionHistoryController@index')->name('cashCollectionHistory.index');
            Route::post('/cash-collection-history-print', 'Admin\CashCollectionHistoryController@print')->name('cashCollectionHistory.print');

            // datewise collection 
            Route::get('collection-date-wise', 'Admin\CashCollectionHistoryController@dateWiseCollection')->name('dateWiseCollection');
            // datewise sales 
            Route::get('sales-date-wise', 'Admin\CashCollectionHistoryController@dateWiseSales')->name('dateWiseSales');

            // AccountMargeController
            Route::any('account-marge', 'Admin\AccountMargeController@index')->name('accountMarge.view');
            Route::post('account-marge-save', 'Admin\AccountMargeController@save')->name('accountMarge.save');




            //Group wise sales target and achievement
            Route::get('/group-wsie-target-achivement', 'Admin\GroupSalesTargetAchivementController@index')->name('groupSalesTargetAchivement.index');
            Route::post('/group-wsie-target-achivement', 'Admin\GroupSalesTargetAchivementController@index')->name('groupSalesTargetAchivement.index');
            Route::post('/group-wsie-target-achivement-print', 'Admin\GroupSalesTargetAchivementController@print')->name('groupSalesTargetAchivement.print');



            //Salary achievement
            Route::get('salary-achivement', 'Admin\SalaryAchivementController@index')->name('salaryAchivement.index');
            Route::post('salary-achivement', 'Admin\SalaryAchivementController@index')->name('salaryAchivement.index');
            Route::post('salary-achivement-print', 'Admin\SalaryAchivementController@print')->name('salaryAchivement.print');

            // End Sales Management
            // Start Installment Management

            /* Installment schedule section */
            Route::get('/installment-schedule', 'Admin\InstallmentScheduleController@index')->name('installmentSchedule.index');

            Route::get('/installment-schedule-add', 'Admin\InstallmentScheduleController@add')->name('installmentSchedule.add');
            Route::post('/installment-schedule-save', 'Admin\InstallmentScheduleController@save')->name('installmentSchedule.save');

            Route::get('/installment-schedule-edit/{id}', 'Admin\InstallmentScheduleController@edit')->name('installmentSchedule.edit');
            Route::post('/installment-schedule-update', 'Admin\InstallmentScheduleController@update')->name('installmentSchedule.update');

            Route::post('/installment-schedule-delete', 'Admin\InstallmentScheduleController@delete')->name('installmentSchedule.delete');

            Route::post('/installment-schedule-get-customerproduct-info', 'Admin\InstallmentScheduleController@getCustomerProductInfo')->name('installmentSchedule.getCustomerProductInfo');

            Route::get('/installment-schedule-print/{id}', 'Admin\InstallmentScheduleController@print')->name('installmentSchedule.print');

            //End Installment Module

            /* Installment  collection */
            Route::get('/installment-collection', 'Admin\InstallmentCollectionController@index')->name('installmentCollection.index');

            Route::get('/installment-collection-add', 'Admin\InstallmentCollectionController@add')->name('installmentCollection.add');
            Route::post('/installment-collection-save', 'Admin\InstallmentCollectionController@save')->name('installmentCollection.save');

            Route::get('/installment-collection-edit/{id}', 'Admin\InstallmentCollectionController@edit')->name('installmentCollection.edit');
            Route::post('/installment-collection-update', 'Admin\InstallmentCollectionController@update')->name('installmentCollection.update');

            Route::post('/installment-collection-delete', 'Admin\InstallmentCollectionController@delete')->name('installmentCollection.delete');

            Route::post('/installment-collection-get-installment-info', 'Admin\InstallmentCollectionController@getInstallmentInfo')->name('installmentCollection.getInstallmentInfo');

            //Drop Collection List
            Route::get('/drop-collection-list', 'Admin\DropCollectionController@index')->name('dropCollection.index');
            Route::post('/drop-collection-list', 'Admin\DropCollectionController@index')->name('dropCollection.index');
            Route::post('/drop-collection-list-print', 'Admin\DropCollectionController@print')->name('dropCollection.print');

            //Prospective Collection List
            Route::get('/prospective-collection-list', 'Admin\ProspectiveCollectionController@index')->name('prospectiveCollection.index');
            Route::post('/prospective-collection-list', 'Admin\ProspectiveCollectionController@index')->name('prospectiveCollection.index');
            Route::post('/prospective-collection-list-print', 'Admin\ProspectiveCollectionController@print')->name('prospectiveCollection.print');

            //Collection History List
            Route::get('/collection-history', 'Admin\CollectionHistoryController@index')->name('collectionHistory.index');
            Route::post('/collection-history', 'Admin\CollectionHistoryController@index')->name('collectionHistory.index');
            Route::post('/collection-history-print', 'Admin\CollectionHistoryController@print')->name('collectionHistory.print');

            // End Installment Management
            // 

            Route::post('territory-area-region', 'Admin\RegionSetupController@areaRegionTerritory')->name('areaRegionTerritory');

            // 
            // 
            // Start Dealer Management
            // Dealer Setup
            Route::get('/dealer-setup', 'Admin\DealerSetupController@index')->name('dealerSetup.index');
            Route::get('/dealer-setup-add', 'Admin\DealerSetupController@add')->name('dealerSetup.add');
            Route::post('/dealer-setup-save', 'Admin\DealerSetupController@save')->name('dealerSetup.save');
            Route::get('/dealer-setup-edit/{id}', 'Admin\DealerSetupController@edit')->name('dealerSetup.edit');
            Route::post('/dealer-setup-update', 'Admin\DealerSetupController@update')->name('dealerSetup.update');
            Route::post('/dealer-setup-delete', 'Admin\DealerSetupController@delete')->name('dealerSetup.delete');
            Route::post('/dealer-setup-status', 'Admin\DealerSetupController@changeStatus')->name('dealerSetup.status');
            Route::post('/dealer-setup/upazila-list', 'Admin\DealerSetupController@getAllUpazilaByDistrict')->name('dealerSetup.getAllUpazilaByDistrict');

            Route::get('/all-dealer-list', 'Admin\DealerSetupController@print')->name('dealerSetup.print');

            Route::post('/dealer-setup/teritori-list', 'Admin\DealerSetupController@getAllTeritoryByArea')->name('dealerSetup.getAllTeritoryByArea');

            //Dealer Documents
            Route::post('upload-document', 'Admin\DealerSetupController@saveDocument')->name('dealerDocument.save');
            Route::post('remove-document', 'Admin\DealerSetupController@deleteDocument')->name('dealerDocument.delete');
            Route::get('dealer-document-download/{id}', 'Admin\DealerSetupController@download')->name('dealerDocument.download');
            Route::get('dealer-view-document/{id}', 'Admin\DealerSetupController@documentView')->name('dealerDocument.view');



            //Dealer List
            Route::get('dealer-list', 'Admin\DealerListController@index')->name('dealerList.index');
            Route::post('dealer-list', 'Admin\DealerListController@index')->name('dealerList.index');
            Route::post('dealer-list-print', 'Admin\DealerListController@print')->name('dealerList.print');

            Route::post('dealer-list-area', 'Admin\DealerListController@dealerListArea')->name('dealerList.area');
            Route::post('dealer-list-territory', 'Admin\DealerListController@dealerListTerritory')->name('dealerList.territory');




            //Dealer Commission
            Route::get('dealer-commission', 'Admin\DealerCommissionController@index')->name('dealerCommission.index');
            Route::get('dealer-commission-add', 'Admin\DealerCommissionController@add')->name('dealerCommission.add');
            Route::post('dealer-commission-save', 'Admin\DealerCommissionController@save')->name('dealerCommission.save');
            Route::get('dealer-commission-edit/{id}', 'Admin\DealerCommissionController@edit')->name('dealerCommission.edit');
            Route::post('dealer-commission-update', 'Admin\DealerCommissionController@update')->name('dealerCommission.update');
            Route::post('dealer-commission-status', 'Admin\DealerCommissionController@status')->name('dealerCommission.status');
            Route::post('dealer-commission-delete', 'Admin\DealerCommissionController@delete')->name('dealerCommission.delete');



            //Dealer Transfer
            Route::get('dealer-transfer', 'Admin\DealerTransferController@index')->name('dealerTransfer.index');
            Route::get('dealer-transfer/add', 'Admin\DealerTransferController@add')->name('dealerTransfer.add');
            Route::post('dealer-transfer/save', 'Admin\DealerTransferController@save')->name('dealerTransfer.save');
            Route::get('dealer-transfer-edit/{id}', 'Admin\DealerTransferController@edit')->name('dealerTransfer.edit');
            Route::post('dealer-transfer/update', 'Admin\DealerTransferController@update')->name('dealerTransfer.update');
            Route::post('dealer-transfer-employee', 'Admin\DealerTransferController@getEmployee')->name('dealerTransfer.getEmployee');


            // Dealer Product Requisition
            Route::get('/dealer-requisition', 'Admin\DealerRequisitionController@index')->name('dealerRequisition.index');
            Route::get('/dealer-requisition-add', 'Admin\DealerRequisitionController@add')->name('dealerRequisition.add');
            Route::post('/dealer-requisition-save', 'Admin\DealerRequisitionController@save')->name('dealerRequisition.save');
            Route::get('/dealer-requisition-edit/{id}', 'Admin\DealerRequisitionController@edit')->name('dealerRequisition.edit');
            Route::post('/dealer-requisition-update', 'Admin\DealerRequisitionController@update')->name('dealerRequisition.update');
            Route::post('/dealer-requisition-delete', 'Admin\DealerRequisitionController@delete')->name('dealerRequisition.delete');
            Route::post('/dealer-requisition-status', 'Admin\DealerRequisitionController@changeStatus')->name('dealerRequisition.status');
            Route::post('/dealer-requisition/product-info', 'Admin\DealerRequisitionController@productInfo')->name('dealerRequisition.productInfo');
            Route::post('/dealer-requisition/requisition-product-info', 'Admin\DealerRequisitionController@requisitionProductInfo')->name('dealerRequisition.requisitionProductInfo');

            // dealer requisition suggestion
            Route::get('/dealer-requisition-suggestion', 'Admin\DealerRequisitionApprovalController@suggestionIndex')->name('dealerRequisitionSuggestion.index');
            Route::post('/dealer-requisition-suggestion-update', 'Admin\DealerRequisitionApprovalController@suggestionUpdate')->name('dealerRequisitionSuggestion.update');

            // Dealer Requisition Approval
            Route::get('/dealer-requisition-approval', 'Admin\DealerRequisitionApprovalController@index')->name('dealerRequisitionApproval.index');
            Route::post('/dealer-requisition-approval-update', 'Admin\DealerRequisitionApprovalController@update')->name('dealerRequisitionApproval.update');
            Route::post('/dealer-requisition-approval/dealer-requisition-info', 'Admin\DealerRequisitionApprovalController@dealerRequisitionInfo')->name('dealerRequisitionApproval.dealerRequisitionInfo');

            // Dealer Final Requisition Approval
            Route::get('/dealer-final-requisition-approval', 'Admin\DealerFinalApprovalController@index')->name('dealerFinalRequisitionApproval.index');
            Route::post('/dealer-final-requisition-approval-update', 'Admin\DealerFinalApprovalController@update')->name('dealerFinalRequisitionApproval.update');
            Route::post('/dealer-final-requisition-approval/dealer-requisition-info', 'Admin\DealerFinalApprovalController@dealerRequisitionInfo')->name('dealerFinalRequisitionApproval.dealerRequisitionInfo');

            // Dealer Product Issue
            Route::get('/product-issue', 'Admin\ProductIssueController@index')->name('productIssue.index');
            Route::get('/product-issue-add', 'Admin\ProductIssueController@add')->name('productIssue.add');
            Route::post('/product-issue-save', 'Admin\ProductIssueController@save')->name('productIssue.save');
            Route::get('/product-issue-edit/{id}', 'Admin\ProductIssueController@edit')->name('productIssue.edit');
            Route::post('/product-issue-update', 'Admin\ProductIssueController@update')->name('productIssue.update');
            Route::post('/product-issue-delete', 'Admin\ProductIssueController@delete')->name('productIssue.delete');
            Route::post('/product-issue-status', 'Admin\ProductIssueController@changeStatus')->name('productIssue.status');
            Route::post('/product-issue/product-info', 'Admin\ProductIssueController@productInfo')->name('productIssue.productInfo');
            Route::post('/product-issue/consumer-product-info', 'Admin\ProductIssueController@consumerProductInfo')->name('productIssue.consumerProductInfo');
            Route::post('/product-issue/product-serial-info', 'Admin\ProductIssueController@productSerialInfo')->name('productIssue.productSerialInfo');
            Route::post('/product-issue/dealer-requisition-info', 'Admin\ProductIssueController@dealerRequisitionInfo')->name('productIssue.dealerRequisitionInfo');
            Route::get('/product-issue-print-chalan/{productIssueId}', 'Admin\ProductIssueController@printChalan')->name('productIssue.printChalan');
            Route::get('/product-issue-print-invoice/{productIssueId}', 'Admin\ProductIssueController@printInvoice')->name('productIssue.printInvoice');

            Route::post('/product-issue/get-product', 'Admin\ProductIssueController@getProduct')->name('productIssue.getProduct');
            Route::post('/product-issue/get-requisition-products', 'Admin\ProductIssueController@getRequisitionProduct')->name('productIssue.getRequisitionProduct');

            //Dealer Collection setup
            Route::get('/dealer-collection', 'Admin\DealerCollectionController@index')->name('dealerCollection.index');
            Route::get('/dealer-collection-add', 'Admin\DealerCollectionController@add')->name('dealerCollection.add');
            Route::post('/dealer-collection-save', 'Admin\DealerCollectionController@save')->name('dealerCollection.save');
            Route::get('/dealer-collection-edit/{id}', 'Admin\DealerCollectionController@edit')->name('dealerCollection.edit');
            Route::post('/dealer-collection-update', 'Admin\DealerCollectionController@update')->name('dealerCollection.update');
            Route::post('/dealer-collection-delete', 'Admin\DealerCollectionController@delete')->name('dealerCollection.delete');
            Route::get('/dealer-advance-delete/{id}', 'Admin\DealerCollectionController@AdvanceDelete')->name('dealerAdvance.delete');
            Route::post('/dealer-collection/product-issue-info', 'Admin\DealerCollectionController@getProductIssueInfo')->name('dealerCollection.getProductIssueInfo');
            Route::post('/dealer-collection/dealer-info', 'Admin\DealerCollectionController@getDealerInfo')->name('dealerCollection.getDealerInfo');
            Route::get('/dealer-collection-print-money-receipt/{dealerCollectioId}', 'Admin\DealerCollectionController@printMoneyReceipt')->name('dealerCollection.printMoneyReceipt');
            Route::post('/dealer-collection/get-all-product-info', 'Admin\DealerCollectionController@getAllProductInfo')->name('dealerCollection.getAllProductInfo');
            Route::post('/dealer-collection/get-dealer-staffs', 'Admin\DealerCollectionController@getDealerStaffs')->name('dealerCollection.get.dealer.staffs');
            Route::post('dealer/adjust-amount', 'Admin\DealerCollectionController@getAdjustAmount')->name('dealer.adjust.amount');

            //Dealer Collection Statement
            Route::get('/dealer-commission-statement', 'Admin\DealerCommissionStatementController@index')->name('dealerCommissionStatement.index');
            Route::post('/dealer-commission-statement', 'Admin\DealerCommissionStatementController@index')->name('dealerCommissionStatement.index');
            Route::post('/dealer-commission-statement-print', 'Admin\DealerCommissionStatementController@print')->name('dealerCommissionStatement.print');

            //Product Requisition And Approval Statement
            Route::get('/product-requisition-approval-statement', 'Admin\ProductRequisitionApprovalStatementController@index')->name('productRequisitionApprovalStatement.index');
            Route::post('/product-requisition-approval-statement', 'Admin\ProductRequisitionApprovalStatementController@index')->name('productRequisitionApprovalStatement.index');
            Route::post('/product-requisition-approval-statement-print', 'Admin\ProductRequisitionApprovalStatementController@print')->name('productRequisitionApprovalStatement.print');

            //Dealer Collection History
            Route::get('/dealer-collection-history', 'Admin\DealerCollectionHistoryController@index')->name('dealerCollectionHistory.index');
            Route::post('/dealer-collection-history', 'Admin\DealerCollectionHistoryController@index')->name('dealerCollectionHistory.index');
            Route::post('/dealer-collection-history-print', 'Admin\DealerCollectionHistoryController@print')->name('dealerCollectionHistory.print');

            //Product Issue History
            Route::get('/product-issue-history', 'Admin\ProductIssueHistoryController@index')->name('productIssueHistory.index');
            Route::post('/product-issue-history', 'Admin\ProductIssueHistoryController@index')->name('productIssueHistory.index');
            Route::post('/product-issue-history-print', 'Admin\ProductIssueHistoryController@print')->name('productIssueHistory.print');
            Route::any('/get-dealer-by-type', 'Admin\ProductIssueHistoryController@getDealerByType')->name('dealer.get.by.type');
            Route::post('product-issue-history-dealer-area', 'Admin\ProductIssueHistoryController@dealerArea')->name('productIssueHistory.dealerArea');

            //sales return history 
            Route::get('/sales-return', 'Admin\SalesReturnHistoryController@index')->name('salesReturnHistory.index');
            Route::post('/sales-return', 'Admin\SalesReturnHistoryController@index')->name('salesReturnHistory.index');
            Route::get('/print-sales-return-invoice/{invoiceId}', 'Admin\SalesReturnHistoryController@printInvoice')->name('salesReturn.printInvoice');
            Route::get('/print-sales-return-chalan/{invoiceId}', 'Admin\SalesReturnHistoryController@printChalan')->name('salesReturn.printChalan');
            Route::get('/sales-return-history', 'Admin\SalesReturnHistoryController@dealerHistory')->name('salesReturnHistory.history');
            Route::post('/sales-return-history', 'Admin\SalesReturnHistoryController@dealerHistory')->name('salesReturnHistory.history');
            Route::post('/sales-return-history-print', 'Admin\SalesReturnHistoryController@print')->name('salesReturnHistory.print');

            //Product Sales Return
            Route::get('/sales-return-add', 'Admin\SalesReturnController@index')->name('salesReturn.index');
            Route::get('/sales-return-view/{id}', 'Admin\SalesReturnController@salesReturnView')->name('salesReturn.view');
            Route::get('/sales-return/{id}', 'Admin\SalesReturnController@salesReturn')->name('sales.return');
            Route::post('/sales-return-save', 'Admin\SalesReturnController@save')->name('salesReturn.save');
            Route::get('/sales-return-save-multiple', 'Admin\SalesReturnController@salesReturnMultiple')->name('salesReturn.multiple');
            Route::post('/sales-return-save-multiple', 'Admin\SalesReturnController@saveMultiple')->name('salesReturn.save.multiple');
            Route::get('/sales-exchange/{id}', 'Admin\SalesReturnController@salesExchange')->name('sales.exchange');
            Route::post('/sales-return-delete', 'Admin\SalesReturnController@delete')->name('salesReturn.delete');
            Route::get('/sales-return-edit/{id}', 'Admin\SalesReturnController@edit')->name('salesReturn.edit');
            Route::post('/sales-return-update', 'Admin\SalesReturnController@update')->name('salesReturn.update');


            //Dealer Statement
            Route::get('/dealer-statement', 'Admin\DealerStatementController@index')->name('dealerStatement.index');
            Route::post('/dealer-statement', 'Admin\DealerStatementController@index')->name('dealerStatement.index');
            Route::post('/dealer-statement-print', 'Admin\DealerStatementController@print')->name('dealerStatement.print');

            //Dealer Realization
            Route::get('/dealer-realization', 'Admin\DealerRealizationController@index')->name('dealerRealization.index');
            Route::post('/dealer-realization', 'Admin\DealerRealizationController@index')->name('dealerRealization.index');
            Route::post('/dealer-realization-print', 'Admin\DealerRealizationController@print')->name('dealerRealization.print');

            //Dealer Sales Contribution
            Route::get('/dealer-sales-contribution', 'Admin\DealerSalesContributionController@index')->name('dealerSalesContribution.index');
            Route::post('/dealer-sales-contribution', 'Admin\DealerSalesContributionController@index')->name('dealerSalesContribution.index');
            Route::post('/dealer-sales-contribution-print', 'Admin\DealerSalesContributionController@print')->name('dealerSalesContribution.print');

            //Sales State Report
            Route::get('/sales-state-report', 'Admin\SaleStateReportController@index')->name('sale.state.index');
            Route::post('/sales-state-report', 'Admin\SaleStateReportController@index')->name('sale.state.index');
            Route::post('/sales-state-report-print', 'Admin\SaleStateReportController@print')->name('sale.state.print');

            //Retails Sales State Report
            Route::get('retails-sales-state-report', 'Admin\RertailsSaleStateReportController@index')->name('retailsale.state.index');
            Route::post('retails-sales-state-report', 'Admin\RertailsSaleStateReportController@index')->name('retailsale.state.index');
            Route::post('retails-sales-state-report-print', 'Admin\RertailsSaleStateReportController@print')->name('retailsale.state.print');

            //Dealer Wise Product Contribution
            Route::get('/dealer-wise-product-contribution', 'Admin\DealerWiseProductContributionController@index')->name('dealerWiseProductContribution.index');
            Route::post('/dealer-wise-product-contribution', 'Admin\DealerWiseProductContributionController@index')->name('dealerWiseProductContribution.index');
            Route::post('/dealer-wise-product-contribution-print', 'Admin\DealerWiseProductContributionController@print')->name('dealerWiseProductContribution.print');
            // End Dealer Management
            // Start Account Management
            // COA Setup
            Route::get('/coa-setup', 'Admin\CoaSetupController@index')->name('coaSetup.index');
            Route::get('/coa-setup-add', 'Admin\CoaSetupController@add')->name('coaSetup.add');
            Route::post('/coa-setup-action', 'Admin\CoaSetupController@action')->name('coaSetup.action');
            Route::get('/coa-setup-edit/{id}', 'Admin\CoaSetupController@edit')->name('coaSetup.edit');
            Route::post('/coa-setup-update', 'Admin\CoaSetupController@update')->name('coaSetup.update');
            Route::post('/coa-setup-delete', 'Admin\CoaSetupController@delete')->name('coaSetup.delete');
            Route::post('/coa-setup-tree', 'Admin\CoaSetupController@makeTree')->name('coaSetup.makeTree');
            Route::post('/coa-setup-new-coa-data', 'Admin\CoaSetupController@newCoaData')->name('coaSetup.newCoaData');
            Route::post('/coa-setup-load-coa-data', 'Admin\CoaSetupController@loadCoaData')->name('coaSetup.loadCoaData');
            Route::post('/coa-get-tree', 'Admin\CoaSetupController@loadCoaTree')->name('coaSetup.loadCoaTree');

            // Debit Voucher Entry
            Route::get('/debit-entry', 'Admin\DebitEntryController@index')->name('debitEntry.index');
            Route::post('/debit-entry', 'Admin\DebitEntryController@index')->name('debitEntry.index');
            Route::post('/debit-entry-print', 'Admin\DebitEntryController@print')->name('debitEntry.print');
            Route::get('/debit-entry-add', 'Admin\DebitEntryController@add')->name('debitEntry.add');
            Route::post('/debit-entry-save', 'Admin\DebitEntryController@save')->name('debitEntry.save');
            Route::get('/debit-entry-view/{id}', 'Admin\DebitEntryController@view')->name('debitEntry.view');
            Route::get('/debit-entry-print/{id}', 'Admin\DebitEntryController@printDebitVoucher')->name('journalEntry.printDebitVoucher');
            Route::get('/debit-entry-edit/{id}', 'Admin\DebitEntryController@edit')->name('debitEntry.edit');
            Route::post('/debit-entry-update', 'Admin\DebitEntryController@update')->name('debitEntry.update');
            Route::post('/debit-entry-delete', 'Admin\DebitEntryController@delete')->name('debitEntry.delete');
            Route::post('/debit-entry-publish', 'Admin\DebitEntryController@changePublish')->name('debitEntry.publish');
            Route::post('/debit-entry-get-coa', 'Admin\DebitEntryController@getCoa')->name('debitEntry.getCoa');
            Route::post('/debit-entry-vouchar-no', 'Admin\DebitEntryController@getVoucharNo')->name('debitEntry.getVoucharNo');

            // Credit Voucher Entry
            Route::get('/credit-entry', 'Admin\CreditEntryController@index')->name('creditEntry.index');
            Route::post('/credit-entry', 'Admin\CreditEntryController@index')->name('creditEntry.index');
            Route::post('/credit-entry-print', 'Admin\CreditEntryController@print')->name('creditEntry.print');
            Route::get('/credit-entry-add', 'Admin\CreditEntryController@add')->name('creditEntry.add');
            Route::post('/credit-entry-save', 'Admin\CreditEntryController@save')->name('creditEntry.save');
            Route::get('/credit-entry-view/{id}', 'Admin\CreditEntryController@view')->name('creditEntry.view');
            Route::get('/credit-entry-print/{id}', 'Admin\CreditEntryController@printCreditVoucher')->name('journalEntry.printCreditVoucher');
            Route::get('/credit-entry-edit/{id}', 'Admin\CreditEntryController@edit')->name('creditEntry.edit');
            Route::post('/credit-entry-update', 'Admin\CreditEntryController@update')->name('creditEntry.update');
            Route::post('/credit-entry-delete', 'Admin\CreditEntryController@delete')->name('creditEntry.delete');
            Route::post('/credit-entry-publish', 'Admin\CreditEntryController@changePublish')->name('creditEntry.publish');
            Route::post('/credit-entry-get-coa', 'Admin\CreditEntryController@getCoa')->name('creditEntry.getCoa');
            Route::post('/credit-entry-vouchar-no', 'Admin\CreditEntryController@getVoucharNo')->name('creditEntry.getVoucharNo');

            // Journal Voucher Entry
            Route::get('/journal-entry', 'Admin\JournalEntryController@index')->name('journalEntry.index');
            Route::post('/journal-entry', 'Admin\JournalEntryController@index')->name('journalEntry.index');
            Route::post('/journal-entry-print', 'Admin\JournalEntryController@print')->name('journalEntry.print');
            Route::get('/journal-entry-add', 'Admin\JournalEntryController@add')->name('journalEntry.add');
            Route::post('/journal-entry-save', 'Admin\JournalEntryController@save')->name('journalEntry.save');
            Route::get('/journal-entry-view/{id}', 'Admin\JournalEntryController@view')->name('journalEntry.view');
            Route::get('/journal-entry-print/{id}', 'Admin\JournalEntryController@printJournalVoucher')->name('journalEntry.printJournalVoucher');
            Route::get('/journal-entry-edit/{id}', 'Admin\JournalEntryController@edit')->name('journalEntry.edit');
            Route::post('/journal-entry-update', 'Admin\JournalEntryController@update')->name('journalEntry.update');
            Route::post('/journal-entry-delete', 'Admin\JournalEntryController@delete')->name('journalEntry.delete');
            Route::post('/journal-entry-publish', 'Admin\JournalEntryController@changePublish')->name('journalEntry.publish');
            Route::post('/journal-entry-get-coa', 'Admin\JournalEntryController@getCoa')->name('journalEntry.getCoa');
            Route::post('/journal-entry-vouchar-no', 'Admin\JournalEntryController@getVoucharNo')->name('journalEntry.getVoucharNo');

            // Openning Balance Entry
            Route::get('/opening-balance-entry', 'Admin\OpeningBalanceController@index')->name('openingBalanceEntry.index');
            Route::post('/opening-balance-entry', 'Admin\OpeningBalanceController@index')->name('openingBalanceEntry.index');
            Route::post('/opening-balance-entry-print', 'Admin\OpeningBalanceController@print')->name('openingBalanceEntry.print');
            Route::get('/opening-balance-entry-add', 'Admin\OpeningBalanceController@add')->name('openingBalanceEntry.add');
            Route::post('/opening-balance-entry-save', 'Admin\OpeningBalanceController@save')->name('openingBalanceEntry.save');
            Route::get('/opening-balance-entry-view/{id}', 'Admin\OpeningBalanceController@view')->name('openingBalanceEntry.view');
            Route::get('/opening-balance-entry-print/{id}', 'Admin\OpeningBalanceController@printOpeningBalanceVoucher')->name('openingBalanceEntry.printOpeningBalanceVoucher');
            Route::post('/opening-balance-entry-delete', 'Admin\OpeningBalanceController@delete')->name('openingBalanceEntry.delete');
            Route::post('/opening-balance-entry-publish', 'Admin\OpeningBalanceController@changePublish')->name('openingBalanceEntry.publish');
            Route::post('/opening-balance-entry-get-coa', 'Admin\OpeningBalanceController@getCoa')->name('openingBalanceEntry.getCoa');
            Route::post('/opening-balance-entry-vouchar-no', 'Admin\OpeningBalanceController@getVoucharNo')->name('openingBalanceEntry.getVoucharNo');

            // Voucher Approve
            Route::get('/voucher-approve', 'Admin\VoucherApproveController@index')->name('voucherApprove.index');
            // Route::post('/voucher-approve', 'Admin\VoucherApproveController@index')->name('voucherApprove.index');
            Route::get('/voucher-approve-view/{id}', 'Admin\VoucherApproveController@view')->name('voucherApprove.view');
            Route::post('/voucher-approve', 'Admin\VoucherApproveController@approve')->name('voucherApprove.approve');

            // Voucher Refuse
            Route::get('/voucher-refuse', 'Admin\VoucherRefuseController@index')->name('voucherRefuse.index');
            // Route::post('/voucher-refuse', 'Admin\VoucherRefuseController@index')->name('voucherRefuse.index');
            Route::get('/voucher-refuse-view/{id}', 'Admin\VoucherRefuseController@view')->name('voucherRefuse.view');
            Route::post('/voucher-refuse', 'Admin\VoucherRefuseController@approve')->name('voucherRefuse.approve');

            // End Account Management
            // Start Account Report
            // COA List
            Route::get('/coa-list', 'Admin\CoaListController@index')->name('coaList.index');
            Route::get('/coa-list-print', 'Admin\CoaListController@print')->name('coaList.print');

            //Voucher List
            Route::get('/voucher-list', 'Admin\VoucherListController@index')->name('voucherList.index');
            Route::post('/voucher-list', 'Admin\VoucherListController@index')->name('voucherList.index');
            Route::post('/voucher-list-print', 'Admin\VoucherListController@print')->name('voucherList.print');

            //General Ledger List
            Route::get('/general-ledger', 'Admin\GeneralLedgerController@index')->name('generalLedger.index');
            Route::post('/general-ledger', 'Admin\GeneralLedgerController@index')->name('generalLedger.index');
            Route::post('/general-ledger-print', 'Admin\GeneralLedgerController@print')->name('generalLedger.print');

            //Trasaction Ledger List
            Route::get('/transaction-ledger', 'Admin\TransactionLedgerController@index')->name('transactionLedger.index');
            Route::post('/transaction-ledger', 'Admin\TransactionLedgerController@index')->name('transactionLedger.index');
            Route::post('/transaction-ledger-print', 'Admin\TransactionLedgerController@print')->name('transactionLedger.print');

            //Cash Book
            Route::get('/cash-book', 'Admin\CashBookController@index')->name('cashBook.index');
            Route::post('/cash-book', 'Admin\CashBookController@index')->name('cashBook.index');
            Route::post('/cash-book-print', 'Admin\CashBookController@print')->name('cashBook.print');

            //Bank Book
            Route::get('/bank-book', 'Admin\bankBookController@index')->name('bankBook.index');
            Route::post('/bank-book', 'Admin\bankBookController@index')->name('bankBook.index');
            Route::post('/bank-book-print', 'Admin\bankBookController@print')->name('bankBook.print');

            //Trial Balance
            Route::get('/trial-balance', 'Admin\TrialBalanceController@index')->name('trialBalance.index');
            Route::post('/trial-balance', 'Admin\TrialBalanceController@index')->name('trialBalance.index');
            Route::post('/trial-balance-print', 'Admin\TrialBalanceController@print')->name('trialBalance.print');

            //Balance Sheets
            Route::get('/balance-sheet', 'Admin\BalanceSheetController@index')->name('balanceSheet.index');
            Route::get('/balance-sheet-print', 'Admin\BalanceSheetController@print')->name('balanceSheet.print');


            //Depriciation
            Route::get('depreciation', 'Admin\DepriciationController@index')->name('depriciation.index');
            Route::post('depreciation', 'Admin\DepriciationController@index')->name('depriciation.index');
            Route::post('depreciation-print', 'Admin\DepriciationController@print')->name('depriciation.print');


            //Bank Reconciliation
            Route::get('reconciliation-entry', 'Admin\ReconciliationEntryController@index')->name('reconciliationEntry.index');
            Route::post('reconciliation-entry', 'Admin\ReconciliationEntryController@index')->name('reconciliationEntry.index');
            Route::post('reconciliationl-entry-print', 'Admin\ReconciliationEntryController@print')->name('reconciliationEntry.print');
            Route::get('reconciliation-entry-add', 'Admin\ReconciliationEntryController@add')->name('reconciliationEntry.add');
            Route::post('reconciliation-entry-save', 'Admin\ReconciliationEntryController@save')->name('reconciliationEntry.save');
            Route::get('reconciliation-entry-view/{id}', 'Admin\ReconciliationEntryController@view')->name('reconciliationEntry.view');
            Route::get('reconciliation-entry-print/{id}', 'Admin\ReconciliationEntryController@printJournalVoucher')->name('reconciliationEntry.printJournalVoucher');
            Route::get('reconciliation-entry-edit/{id}', 'Admin\ReconciliationEntryController@edit')->name('reconciliationEntry.edit');
            Route::post('reconciliation-entry-update', 'Admin\ReconciliationEntryController@update')->name('reconciliationEntry.update');
            Route::post('reconciliation-entry-delete', 'Admin\ReconciliationEntryController@delete')->name('reconciliationEntry.delete');
            Route::post('reconciliation-entry-publish', 'Admin\ReconciliationEntryController@changePublish')->name('reconciliationEntry.publish');


            //Income statement
            Route::get('/income-statement', 'Admin\IncomeStatementController@index')->name('incomeStatement.index');
            Route::post('/income-statement', 'Admin\IncomeStatementController@index')->name('incomeStatement.index');
            Route::post('/income-statement-print', 'Admin\IncomeStatementController@print')->name('incomeStatement.print');

            //Receive And Payment statement
            Route::get('/receive-payment-statement', 'Admin\ReceivePaymentStatementController@index')->name('receivePaymentStatement.index');
            Route::post('/receive-payment-statement', 'Admin\ReceivePaymentStatementController@index')->name('receivePaymentStatement.index');
            Route::post('/receive-payment-statement-print', 'Admin\ReceivePaymentStatementController@print')->name('receivePaymentStatement.print');
            // End Account Report
            // Start HR & Payroll
            // Leave Setup
            Route::get('/leave-setup', 'Admin\LeaveSetupController@index')->name('leaveSetup.index');
            Route::get('/leave-setup-add', 'Admin\LeaveSetupController@add')->name('leaveSetup.add');
            Route::post('/leave-setup-save', 'Admin\LeaveSetupController@save')->name('leaveSetup.save');
            Route::get('/leave-setup-edit/{id}', 'Admin\LeaveSetupController@edit')->name('leaveSetup.edit');
            Route::post('/leave-setup-update', 'Admin\LeaveSetupController@update')->name('leaveSetup.update');
            Route::post('/leave-setup-delete', 'Admin\LeaveSetupController@delete')->name('leaveSetup.delete');
            Route::post('/leave-setup-status', 'Admin\LeaveSetupController@status')->name('leaveSetup.status');


            // Leave Request
            Route::get('/leave-request', 'Admin\LeaveRequestController@index')->name('leaveRequest.index');
            Route::get('/leave-request-add', 'Admin\LeaveRequestController@add')->name('leaveRequest.add');
            Route::post('/leave-request-save', 'Admin\LeaveRequestController@save')->name('leaveRequest.save');
            Route::get('/leave-request-edit/{id}', 'Admin\LeaveRequestController@edit')->name('leaveRequest.edit');
            Route::post('/leave-request-update', 'Admin\LeaveRequestController@update')->name('leaveRequest.update');
            Route::post('/leave-request-delete', 'Admin\LeaveRequestController@delete')->name('leaveRequest.delete');
            Route::post('/leave-request-status', 'Admin\LeaveRequestController@status')->name('leaveRequest.status');

            Route::post('/leave-info', 'Admin\LeaveRequestController@leaveInfo')->name('leaveRequest.leaveInfo');


            // Leave Request suggest
            Route::get('/leave-request-suggest', 'Admin\LeaveRequestSuggestController@index')->name('leaveRequestSug.index');
            Route::post('/leave-request-suggest-save', 'Admin\LeaveRequestSuggestController@save')->name('leaveRequestSug.save');
            Route::post('/leave-request-suggest-update', 'Admin\LeaveRequestSuggestController@update')->name('leaveRequestSug.update');

            Route::post('/leave-suggest-info', 'Admin\LeaveRequestSuggestController@getSuggestInfo')->name('leaveRequestSug.leaveInfo');


            // Leave Request Approve
            Route::get('/leave-request-approve', 'Admin\LeaveRequestApproveController@index')->name('leaveRequestApp.index');
            Route::post('/leave-request-approve-save', 'Admin\LeaveRequestApproveController@save')->name('leaveRequestApp.save');
            Route::post('/leave-request-approve-update', 'Admin\LeaveRequestApproveController@update')->name('leaveRequestApp.update');

            Route::post('/leave-approve-info', 'Admin\LeaveRequestApproveController@getApproveInfo')->name('leaveRequestApp.leaveInfo');


            // Employee Leave Log
            Route::get('/employee-leave-log', 'Admin\EmployeeLeaveLogController@index')->name('employeeLeaveLog.index');
            Route::post('/employee-leave-log', 'Admin\EmployeeLeaveLogController@index');
            Route::post('/employee-leave-log-print', 'Admin\EmployeeLeaveLogController@print')->name('employeeLeaveLog.print');


            // Allowance Setup
            Route::get('/allowance-setup', 'Admin\AllowanceSetupController@index')->name('allowanceSetup.index');
            Route::get('/allowance-setup-add', 'Admin\AllowanceSetupController@add')->name('allowanceSetup.add');
            Route::post('/allowance-setup-save', 'Admin\AllowanceSetupController@save')->name('allowanceSetup.save');
            Route::get('/allowance-setup-edit/{id}', 'Admin\AllowanceSetupController@edit')->name('allowanceSetup.edit');
            Route::post('/allowance-setup-update', 'Admin\AllowanceSetupController@update')->name('allowanceSetup.update');
            Route::post('/allowance-setup-delete', 'Admin\AllowanceSetupController@delete')->name('allowanceSetup.delete');
            Route::post('/allowance-setup-status', 'Admin\AllowanceSetupController@status')->name('allowanceSetup.status');

            // Designation Setup
            Route::get('/designation-setup', 'Admin\DesignationSetupController@index')->name('designationSetup.index');
            Route::get('/designation-setup-add', 'Admin\DesignationSetupController@add')->name('designationSetup.add');
            Route::post('/designation-setup-save', 'Admin\DesignationSetupController@save')->name('designationSetup.save');
            Route::get('/designation-setup-edit/{id}', 'Admin\DesignationSetupController@edit')->name('designationSetup.edit');
            Route::post('/designation-setup-update', 'Admin\DesignationSetupController@update')->name('designationSetup.update');
            Route::post('/designation-setup-delete', 'Admin\DesignationSetupController@delete')->name('designationSetup.delete');
            Route::post('/designation-setup-status', 'Admin\DesignationSetupController@status')->name('designationSetup.status');

            // Employee Setup
            Route::get('/employee-setup', 'Admin\EmployeeSetupController@index')->name('employeeSetup.index');
            Route::get('/employee-setup-add', 'Admin\EmployeeSetupController@add')->name('employeeSetup.add');
            Route::post('/employee-setup-save', 'Admin\EmployeeSetupController@save')->name('employeeSetup.save');
            Route::get('/employee-setup-edit/{id}', 'Admin\EmployeeSetupController@edit')->name('employeeSetup.edit');
            Route::post('/employee-setup-update', 'Admin\EmployeeSetupController@update')->name('employeeSetup.update');
            Route::get('/employee-setup-view/{id}', 'Admin\EmployeeSetupController@view')->name('employeeSetup.view');
            Route::post('/employee-setup-delete', 'Admin\EmployeeSetupController@delete')->name('employeeSetup.delete');
            Route::post('/employee-setup-status', 'Admin\EmployeeSetupController@status')->name('employeeSetup.status');

            // Employee Leave
            Route::get('/employee-leave', 'Admin\EmployeeLeaveController@index')->name('employeeLeave.index');
            Route::get('/employee-leave-add', 'Admin\EmployeeLeaveController@add')->name('employeeLeave.add');
            Route::post('/employee-leave-save', 'Admin\EmployeeLeaveController@save')->name('employeeLeave.save');
            Route::get('/employee-leave-edit/{id}', 'Admin\EmployeeLeaveController@edit')->name('employeeLeave.edit');
            Route::post('/employee-leave-update', 'Admin\EmployeeLeaveController@update')->name('employeeLeave.update');
            Route::post('/employee-leave-delete', 'Admin\EmployeeLeaveController@delete')->name('employeeLeave.delete');
            Route::post('/employee-leave-status', 'Admin\EmployeeLeaveController@status')->name('employeeLeave.status');
            Route::post('/employee-leave-get-leave-info', 'Admin\EmployeeLeaveController@getLeaveInfo')->name('employeeLeave.getLeaveInfo');

            // Employee Allowance
            Route::get('/employee-allowance', 'Admin\EmployeeAllowanceController@index')->name('employeeAllowance.index');
            Route::get('/employee-allowance-add', 'Admin\EmployeeAllowanceController@add')->name('employeeAllowance.add');
            Route::post('/employee-allowance-save', 'Admin\EmployeeAllowanceController@save')->name('employeeAllowance.save');
            Route::get('/employee-allowance-edit/{id}', 'Admin\EmployeeAllowanceController@edit')->name('employeeAllowance.edit');
            Route::post('/employee-allowance-update', 'Admin\EmployeeAllowanceController@update')->name('employeeAllowance.update');
            Route::post('/employee-allowance-delete', 'Admin\EmployeeAllowanceController@delete')->name('employeeAllowance.delete');
            Route::post('/employee-allowance-status', 'Admin\EmployeeAllowanceController@status')->name('employeeAllowance.status');
            Route::post('/employee-allowance-get-employee-info', 'Admin\EmployeeAllowanceController@getEmployeeInfo')->name('employeeAllowance.getEmployeeInfo');
            Route::post('/employee-allowance-type-info', 'Admin\EmployeeAllowanceController@getTypeInfo')->name('employeeAllowance.getTypeInfo');


            // Monthly Salary Process
            Route::get('monthly-salary-process', 'Admin\MonthlySalaryProcessController@index')->name('monthlySalaryProcess.index');
            Route::post('monthly-salary-process', 'Admin\MonthlySalaryProcessController@index')->name('monthlySalaryProcess.index');
            Route::get('monthly-salary-process-view/{id}', 'Admin\MonthlySalaryProcessController@view')->name('monthlySalaryProcess.view');
            Route::get('monthly-salary-process-print/{id}', 'Admin\MonthlySalaryProcessController@print')->name('monthlySalaryProcess.print');
            Route::get('monthly-salary-view-by-employee/{id}', 'Admin\MonthlySalaryProcessController@viewByEmployee')->name('monthlySalaryProcess.viewByEmployee');
            Route::post('monthly-salary-payment', 'Admin\MonthlySalaryProcessController@payment')->name('monthlySalaryProcess.payment');
            Route::get('monthly-salary-view-by-employee-slip/{id}', 'Admin\MonthlySalaryProcessController@printPaySlip')->name('monthlySalaryProcess.printPaySlip');
            Route::post('monthly-salary-process-delete', 'Admin\MonthlySalaryProcessController@delete')->name('monthlySalaryProcess.delete');


            //Payroll Payment History
            Route::get('payroll-payment-history', 'Admin\PayrollPaymentHistoryController@index')->name('payrollPaymentHistory.index');
            Route::post('payroll-payment-history', 'Admin\PayrollPaymentHistoryController@index')->name('payrollPaymentHistory.index');
            Route::post('payroll-payment-history-print', 'Admin\PayrollPaymentHistoryController@print')->name('payrollPaymentHistory.print');

            // Salary Setup
            Route::get('salary-setup', 'Admin\SalarySetupController@index')->name('salarySetup.index');
            Route::get('salary-setup-add', 'Admin\SalarySetupController@add')->name('salarySetup.add');
            Route::post('salary-setup-save', 'Admin\SalarySetupController@save')->name('salarySetup.save');
            Route::get('salary-setup-edit/{id}', 'Admin\SalarySetupController@edit')->name('salarySetup.edit');
            Route::post('salary-setup-update', 'Admin\SalarySetupController@update')->name('salarySetup.update');
            Route::post('salary-setup-delete', 'Admin\SalarySetupController@delete')->name('salarySetup.delete');
            Route::post('salary-setup-status', 'Admin\SalarySetupController@status')->name('salarySetup.status');



            //holidayssetup
            Route::get('/holidays-setup', 'Admin\HoliController@index')->name('holidaysSetup.index');
            Route::get('/holidays-setup-add', 'Admin\HoliController@add')->name('holidaysSetup.add');
            Route::post('/holidays-setup-save', 'Admin\HoliController@save')->name('holidaysSetup.save');
            Route::get('/holidays-setup-edit/{id}', 'Admin\HoliController@edit')->name('holidaysSetup.edit');
            Route::post('/holidays-setup-update', 'Admin\HoliController@update')->name('holidaysSetup.update');
            Route::post('/holidays-setup-delete', 'Admin\HoliController@delete')->name('holidaysSetup.delete');
            Route::post('/holidays-setup-status', 'Admin\HoliController@status')->name('holidaysSetup.status');

            Route::get('holidays-setup-print', 'Admin\HoliController@print')->name('holidaysSetup.print');

            //leave type

            Route::get('/leave-type', 'Admin\LeaveTypeController@index')->name('leaveType.index');
            Route::get('/leave-type-add', 'Admin\LeaveTypeController@add')->name('leaveType.add');
            Route::post('/leave-type-save', 'Admin\LeaveTypeController@save')->name('leaveType.save');
            Route::get('/leave-type-edit/{id}', 'Admin\LeaveTypeController@edit')->name('leaveType.edit');
            Route::post('/leave-type-update', 'Admin\LeaveTypeController@update')->name('leaveType.update');
            Route::post('/leave-type-delete', 'Admin\LeaveTypeController@delete')->name('leaveType.delete');
            Route::post('/leave-type-status', 'Admin\LeaveTypeController@status')->name('leaveType.status');

            //Manufacture Setup
            Route::get('/manufacture-setup', 'Admin\ManufactureController@index')->name('manufacture.index');
            Route::get('/manufacture-setup-add', 'Admin\ManufactureController@add')->name('manufacture.add');
            Route::post('/manufacture-setup-save', 'Admin\ManufactureController@save')->name('manufacture.save');
            Route::get('/manufacture-setup-edit/{id}', 'Admin\ManufactureController@edit')->name('manufacture.edit');
            Route::post('/manufacture-setup-update', 'Admin\ManufactureController@update')->name('manufacture.update');
            Route::post('/manufacture-setup-delete', 'Admin\ManufactureController@delete')->name('manufacture.delete');
            Route::post('/manufacture-setup-status', 'Admin\ManufactureController@status')->name('manufacture.status');


            //Pro_sales

            Route::get('/proSales', 'Admin\ProSalesController@index')->name('proSales.index');
            Route::get('/proSales-add', 'Admin\ProSalesController@add')->name('proSales.add');
            Route::post('/proSales-save', 'Admin\ProSalesController@save')->name('proSales.save');
            Route::get('/proSales-edit/{id}', 'Admin\ProSalesController@edit')->name('proSales.edit');
            Route::post('/proSales-update', 'Admin\ProSalesController@update')->name('proSales.update');
            Route::post('/proSales-delete', 'Admin\ProSalesController@delete')->name('proSales.delete');
            Route::post('/proSales-product-info', 'Admin\ProSalesController@liftingProductInfo')->name('proSales.productInfo');
            Route::get('/proSales-print/{id}', 'Admin\ProSalesController@print')->name('proSales.print');
            Route::get('/proSales-print-barcode/{id}', 'Admin\ProSalesController@printBarCode')->name('proSales.print.barcode');
            Route::post('/proSales/get-requisition-products', 'Admin\ProSalesController@getRequisitionProduct')->name('proSales.getRequisitionProduct');


            //  Production Requisition Suggestion
            Route::get('/production-requisition-suggestion', 'Admin\ProductionRequitionApprovalContrller@suggestionIndex')->name('productionRequisitionSuggestion.index');
            Route::post('/production-requisition-suggestion-update', 'Admin\ProductionRequitionApprovalContrller@suggestionUpdate')->name('productionRequisitionSuggestion.update');
            Route::post('/production-requisition-approval/production-requisition-info', 'Admin\ProductionRequitionApprovalContrller@productionRequisitionInfo')->name('productionRequisitionApproval.productionRequisitionInfo');

            //  Production Requisition Approval
            Route::get('/production-requisition-approval', 'Admin\ProductionRequitionApprovalContrller@index')->name('productionRequisitionApproval.index');
            Route::post('/production-requisition-approval-update', 'Admin\ProductionRequitionApprovalContrller@update')->name('productionRequisitionApproval.update');


            //Production Requsition Approval-statement
            Route::get('/production-requisition-approval-statement', 'Admin\ProductionRequisitionStatementApprovalController@index')->name('productionRequisitionApprovalStatement.index');
            Route::post('/production-requisition-approval-statement', 'Admin\ProductionRequisitionStatementApprovalController@index')->name('productionRequisitionApprovalStatement.index');
            Route::post('/production-requisition-approval-statement-print', 'Admin\ProductionRequisitionStatementApprovalController@print')->name('productionRequisitionApprovalStatement.print');

            //Production-Issue-History
            Route::get('/production-issue-history', 'Admin\ProductionIssueHistoryController@index')->name('productionIssueHistory.index');
            Route::post('/production-issue-history', 'Admin\ProductionIssueHistoryController@index')->name('productionIssueHistory.index');
            Route::post('/production-issue-history-print', 'Admin\ProductionIssueHistoryController@print')->name('productionIssueHistory.print');


            //Production-History
            Route::get('production-history', 'Admin\ProductionHistoryController@index')->name('productionHistory.index');
            Route::post('production-history', 'Admin\ProductionHistoryController@index')->name('productionHistory.index');
            Route::post('production-history-print', 'Admin\ProductionHistoryController@print')->name('productionHistory.print');


            //Production Vs Requisition
            Route::get('approval-vs-production', 'Admin\ApprovalVsProductionController@index')->name('approvalVsProduction.index');
            Route::post('approval-vs-production', 'Admin\ApprovalVsProductionController@index')->name('approvalVsProduction.index');
            Route::post('approval-vs-production-print', 'Admin\ApprovalVsProductionController@print')->name('approvalVsProduction.print');


            //  Production Requisition
            Route::get('/production-requisition', 'Admin\ProductionRequisitionController@index')->name('productionRequisition.index');
            Route::get('/production-requisition-add', 'Admin\ProductionRequisitionController@add')->name('productionRequisition.add');
            Route::post('/production-requisition-save', 'Admin\ProductionRequisitionController@save')->name('productionRequisition.save');
            Route::get('/production-requisition-edit/{id}', 'Admin\ProductionRequisitionController@edit')->name('productionRequisition.edit');
            Route::post('/production-requisition-update', 'Admin\ProductionRequisitionController@update')->name('productionRequisition.update');
            Route::post('/production-requisition-delete', 'Admin\ProductionRequisitionController@delete')->name('productionRequisition.delete');
            Route::post('/production-requisition-status', 'Admin\ProductionRequisitionController@changeStatus')->name('productionRequisition.status');
            Route::post('/production-requisition/product-info', 'Admin\ProductionRequisitionController@productInfo')->name('productionRequisition.productInfo');
            Route::post('/production-requisition/requisition-product-info', 'Admin\ProductionRequisitionController@requisitionProductInfo')->name('productionRequisition.requisitionProductInfo');




            // Manual Attendance
            Route::get('manual-attendance', 'Admin\ManualAttendanceController@index')->name('manualAttendance.index');
            Route::get('manual-attendance-add', 'Admin\ManualAttendanceController@add')->name('manualAttendance.add');
            Route::post('manual-attendance-save', 'Admin\ManualAttendanceController@save')->name('manualAttendance.save');
            Route::get('manual-attendance-edit/{id}', 'Admin\ManualAttendanceController@edit')->name('manualAttendance.edit');
            Route::post('manual-attendance-update', 'Admin\ManualAttendanceController@update')->name('manualAttendance.update');
            Route::post('manual-attendance-delete', 'Admin\ManualAttendanceController@delete')->name('manualAttendance.delete');


            // Daily Attendance Sheet
            Route::get('daily-attendance-sheet', 'Admin\DailyAttendenceSheetController@index')->name('dailyAttendenceSheet.index');
            Route::post('daily-attendance-sheet', 'Admin\DailyAttendenceSheetController@index');
            Route::post('daily-attendance-sheet-print', 'Admin\DailyAttendenceSheetController@print')->name('dailyAttendenceSheet.print');


            // Monthly Attendance Sheet
            Route::get('monthly-attendance-sheet', 'Admin\MonthlyAttendenceSheetController@index')->name('monthlyAttendenceSheet.index');
            Route::post('monthly-attendance-sheet', 'Admin\MonthlyAttendenceSheetController@index');
            Route::post('monthly-attendance-sheet-print', 'Admin\MonthlyAttendenceSheetController@print')->name('monthlyAttendenceSheet.print');


            // Attendence Summarry
            Route::get('attendance-summary', 'Admin\AttendenceSummaryReportController@index')->name('attendenceSummary.index');
            Route::post('attendance-summary', 'Admin\AttendenceSummaryReportController@index');
            Route::post('attendance-summary-print', 'Admin\AttendenceSummaryReportController@print')->name('attendenceSummary.print');

            Route::post('attendance-summary-in-time', 'Admin\AttendenceSummaryReportController@inTime')->name('attendenceSummary.inTime');
            Route::post('attendance-summary-in-time-print', 'Admin\AttendenceSummaryReportController@inTimePrint')->name('attendenceSummary.inTimePrint');

            Route::post('attendance-summary-late-time', 'Admin\AttendenceSummaryReportController@lateTime')->name('attendenceSummary.lateTime');
            Route::post('attendance-summary-late-time-print', 'Admin\AttendenceSummaryReportController@lateTimePrint')->name('attendenceSummary.lateTimePrint');

            Route::post('attendance-summary-leave', 'Admin\AttendenceSummaryReportController@leave')->name('attendenceSummary.leave');
            Route::post('attendance-summary-leave-print', 'Admin\AttendenceSummaryReportController@leavePrint')->name('attendenceSummary.leavePrint');

            Route::post('attendance-summary-absent', 'Admin\AttendenceSummaryReportController@absent')->name('attendenceSummary.absent');
            Route::post('attendance-summary-absent-print', 'Admin\AttendenceSummaryReportController@absentPrint')->name('attendenceSummary.absentPrint');




            // End HR & Payroll
            // Start Back Data Report
            //Purchase statement
            Route::get('/purchase-report', 'Admin\PreviousPurchaseController@index')->name('prev_purchase.index');
            Route::post('/purchase-report', 'Admin\PreviousPurchaseController@index')->name('prev_purchase.index');
            Route::post('/purchase-report-print', 'Admin\PreviousPurchaseController@print')->name('prev_purchase.print');

            //previous Purchase return statement
            Route::get('/previous-purchase-return-report', 'Admin\PreviousPurchaseReturnController@index')->name('prevPurchaseReturn.index');
            Route::post('/previous-purchase-return-report', 'Admin\PreviousPurchaseReturnController@index')->name('prevPurchaseReturn.index');
            Route::post('/previous-purchase-return-report-print', 'Admin\PreviousPurchaseReturnController@print')->name('prevPurchaseReturn.print');

            
            //DAily Average Report
            Route::get('daily-average-report', 'Admin\DailyAverageReportController@index')->name('dailyAverageReport.index');
            Route::post('daily-average-report', 'Admin\DailyAverageReportController@index')->name('dailyAverageReport.index');
            Route::post('daily-average-report-print', 'Admin\DailyAverageReportController@print')->name('dailyAverageReport.print');


            //Receive And Payment statement
            Route::get('/retail-sales-report', 'Admin\RetailSalesReportController@index')->name('retailSalesReport.index');
            Route::post('/retail-sales-report', 'Admin\RetailSalesReportController@index')->name('retailSalesReport.index');
            Route::post('/retail-sales-report-print', 'Admin\RetailSalesReportController@print')->name('retailSalesReport.print');

            Route::get('/outdstanding-sales-report', 'Admin\RetailSalesReportController@outdstanding_report')->name('outstandingSalesReport.index');
            Route::post('/outdstanding-sales-report', 'Admin\RetailSalesReportController@outdstanding_report')->name('outstandingSalesReport.index');
            Route::post('/outdstanding-sales-report-print', 'Admin\RetailSalesReportController@outdstanding_report_print')->name('outstandingSalesReport.print');

            //Invoice statement
            Route::get('/invoice-report', 'Admin\InvoiceReportController@index')->name('invoiceReport.index');
            Route::post('/invoice-report', 'Admin\InvoiceReportController@index')->name('invoiceReport.index');
            Route::post('/invoice-report-print', 'Admin\InvoiceReportController@print')->name('invoiceReport.print');
            Route::post('/invoice-report-details', 'Admin\InvoiceReportController@details')->name('invoiceReport.details');

            // aging Report
            Route::get('/aging-report', 'Admin\AgingReportController@index')->name('agingReport.index');
            Route::post('/aging-report', 'Admin\AgingReportController@index')->name('agingReport.index');

            // Bulk Collection Retail
            Route::get('/retail-bulk-collection-add', 'Admin\RetailBulkCollectionController@add')->name('bulk.collection.add');
            Route::get('/retail-bulk-collection-get-export-data', 'Admin\RetailBulkCollectionController@getExportData')->name('bulk.collection.getExportData');
            Route::post('/retail-bulk-collection-save', 'Admin\RetailBulkCollectionController@save')->name('bulk.collection.save');
            Route::get('/retail-bulk-collection-get-customer', 'Admin\RetailBulkCollectionController@getCustomers')->name('bulk.collection.getCustomers');


            // Bulk sale Retail
            Route::get('/retail-bulk-sale-add', 'Admin\RetailBulkSaleControler@add')->name('bulk.sale.add');
            Route::get('/retail-bulk-sale-get-export-data', 'Admin\RetailBulkSaleControler@getExportData')->name('bulk.sale.getExportData');
            Route::post('/retail-bulk-sale-save', 'Admin\RetailBulkSaleControler@save')->name('bulk.sale.save');
            Route::get('retail-bulk-sale-customers', 'Admin\RetailBulkSaleControler@getCustomers')->name('bulk.sale.getCustomers');


            // Quick Client Update
            Route::get('/quick-client-update', 'Admin\QuickClientUpdate@index')->name('quick.client.update');
            Route::post('/update-client-references', 'Admin\QuickClientUpdate@updateClientReferences')->name('update.client.references');


            // Audit Accounts
            Route::get('audit-accounts', 'Admin\AuditAccountController@index')->name('audit.index');
            Route::post('update-audit-account', 'Admin\AuditAccountController@updateClientReferences')->name('audit.update');
            Route::post('audit-print', 'Admin\AuditAccountController@print')->name('notAudit.print');

            // Customer Agreement Report
            Route::get('/customer-agreements', 'Admin\QuickClientUpdate@CustomerAgreementReport')->name('customer.agreements.report1');



            Route::get('account-close', 'Admin\AccountCloseController@index')->name('closeAccount.index');
            Route::get('account-close/add', 'Admin\AccountCloseController@add')->name('closeAccount.add');
            Route::post('account-close/save', 'Admin\AccountCloseController@save')->name('closeAccount.save');
            Route::get('account-close/edit/{id}', 'Admin\AccountCloseController@edit')->name('closeAccount.edit');
            Route::post('account-close/update', 'Admin\AccountCloseController@update')->name('closeAccount.update');
            Route::get('account-close/print/{id}', 'Admin\AccountCloseController@print')->name('closeAccount.print');

            Route::post('account-all-details', 'Admin\AccountCloseController@accountInfo')->name('closeAccount.accountInfo');
            Route::post('close-account-info', 'Admin\AccountCloseController@lateDays')->name('closeAccount.lateDays');



            //Unpaid Customers
            Route::get('unpaid-customers', 'Admin\UnpaidCustomerController@index')->name('unpaidcustomer.index');
            Route::post('unpaid-customers', 'Admin\UnpaidCustomerController@index')->name('unpaidcustomer.index');
            Route::post('unpaid-customers-print', 'Admin\UnpaidCustomerController@print')->name('unpaidcustomer.print');


            //Classify Accounts
            Route::get('classify-account', 'Admin\ClassifyAccountController@index')->name('classifyAccount.index');
            Route::post('classify-account', 'Admin\ClassifyAccountController@index')->name('classifyAccount.index');
            Route::post('classify-account-print', 'Admin\ClassifyAccountController@print')->name('classifyAccount.print');



            // Service  Product Receive
            Route::get('service-product-receive', 'Admin\ServiceProductReceiveController@index')->name('serviceProductReceive.index');
            Route::get('service-product-receive-add', 'Admin\ServiceProductReceiveController@add')->name('serviceProductReceive.add');
            Route::post('service-product-receive-save', 'Admin\ServiceProductReceiveController@save')->name('serviceProductReceive.save');
            Route::get('service-product-receive-edit/{id}', 'Admin\ServiceProductReceiveController@edit')->name('serviceProductReceive.edit');
            Route::post('service-product-receive-update', 'Admin\ServiceProductReceiveController@update')->name('serviceProductReceive.update');
            Route::post('service-product-receive-delete', 'Admin\ServiceProductReceiveController@delete')->name('serviceProductReceive.delete');
            Route::post('service-product-receive-product-info', 'Admin\ServiceProductReceiveController@productInfo')->name('serviceProductReceive.productInfo');



            // Service Job Allocation
            Route::get('service-allocation', 'Admin\ServiceAllocationController@index')->name('serviceAllocation.index');
            Route::post('service-allocation-save', 'Admin\ServiceAllocationController@save')->name('serviceAllocation.save');
            Route::get('service-running-allocation', 'Admin\ServiceAllocationController@runningAllocation')->name('serviceAllocation.runningAllocation');
            Route::post('service-running-allocation-save', 'Admin\ServiceAllocationController@runningAllocationSave')->name('serviceAllocation.runningAllocationSave');
            Route::get('service-complete-allocation', 'Admin\ServiceAllocationController@completeAllocation')->name('serviceAllocation.completeAllocation');
            Route::get('service-complete-allocation-print', 'Admin\ServiceAllocationController@completeAllocationPrint')->name('serviceAllocation.completeAllocationPrint');
            Route::post('service-running-allocation-serials', 'Admin\ServiceAllocationController@getSerialNo')->name('serviceAllocation.getSerialNo');



            // Service  Product Delivery
            Route::get('service-product-delivery', 'Admin\ServiceProductDeliveryController@index')->name('serviceDelivery.index');
            Route::get('service-product-delivery-add', 'Admin\ServiceProductDeliveryController@add')->name('serviceDelivery.add');
            Route::post('service-product-delivery-save', 'Admin\ServiceProductDeliveryController@save')->name('serviceDelivery.save');
            Route::get('service-product-delivery-edit/{id}', 'Admin\ServiceProductDeliveryController@edit')->name('serviceDelivery.edit');
            Route::post('service-product-delivery-update', 'Admin\ServiceProductDeliveryController@update')->name('serviceDelivery.update');
            Route::post('service-product-delivery-delete', 'Admin\ServiceProductDeliveryController@delete')->name('serviceDelivery.delete');
            Route::post('service-product-delivery-info', 'Admin\ServiceProductDeliveryController@getInfo')->name('serviceDelivey.getInfo');

            Route::get('service-product-delivery-invoice/{id}', 'Admin\ServiceProductDeliveryController@invoice')->name('serviceDelivey.invoice');
            Route::get('service-product-delivery-chalan/{id}', 'Admin\ServiceProductDeliveryController@chalan')->name('serviceDelivey.chalan');

            // Service  Product Stock
            Route::get('service-product-stock', 'Admin\ServiceProductStockController@index')->name('serviceStock.index');
            Route::get('service-product-stock-print', 'Admin\ServiceProductStockController@print')->name('serviceStock.print');


            // Service Report
            Route::any('service-report', 'Admin\ServiceReportController@index')->name('serviceReport.index');
            Route::post('service-report/print', 'Admin\ServiceReportController@print')->name('serviceReport.print');


            // Dashboard Ajax
            Route::get('top-twelve-dealers', 'HomeController@topTwelveDealers')->name('dashboard.topTwelveDealers');
            Route::get('top-five-dealers', 'HomeController@topFiveDealers')->name('dashboard.topFiveDealers');
            Route::get('top-collections', 'HomeController@topCollections')->name('dashboard.topCollections');
            Route::get('monthly-flow', 'HomeController@monthlyFlow')->name('dashboard.monthlyFlow');
            Route::get('production-lifting', 'HomeController@productionLift')->name('dashboard.productionLift');
            Route::get('home-sales-collection', 'HomeController@salesCollection')->name('dashboard.salesCollection');
            Route::get('home-stock', 'HomeController@stockStaffCus')->name('dashboard.stock');


            //previous Purchase return statement
            Route::get('dealer-oustanding-ratio', 'Admin\DealerOutstandingRatioController@index')->name('DealerOutstandingRatio.index');
            Route::post('dealer-oustanding-ratio', 'Admin\DealerOutstandingRatioController@index')->name('DealerOutstandingRatio.index');
            Route::post('dealer-oustanding-ratio-print', 'Admin\DealerOutstandingRatioController@print')->name('DealerOutstandingRatio.print');


            //Product Wise Report
            Route::get('product-wise-profit', 'Admin\ProductWiseProfitController@index')->name('productWiseProfit.index');
            Route::post('product-wise-profit', 'Admin\ProductWiseProfitController@index')->name('productWiseProfit.index');
            Route::post('product-wise-profit-print', 'Admin\ProductWiseProfitController@print')->name('productWiseProfit.print');


            //Bank Loan
            Route::get('bank-loan', 'Admin\BankLoanController@index')->name('bankLoan.index');
            Route::get('bank-loan-add', 'Admin\BankLoanController@add')->name('bankLoan.add');
            Route::post('bank-loan-save', 'Admin\BankLoanController@save')->name('bankLoan.save');
            Route::get('bank-loan-edit/{id}', 'Admin\BankLoanController@edit')->name('bankLoan.edit');
            Route::post('bank-loan-update', 'Admin\BankLoanController@update')->name('bankLoan.update');
            Route::post('bank-loan-delete', 'Admin\BankLoanController@delete')->name('bankLoan.delete');

            Route::get('bank-loan-schedule', 'Admin\BankLoanController@schedule')->name('bankLoan.schedule');


            //Bank Loan Payment
            Route::get('bank-loan-payment', 'Admin\BankLoanPaymentController@index')->name('bankLoanPayment.index');
            Route::get('bank-loan-payment-add', 'Admin\BankLoanPaymentController@add')->name('bankLoanPayment.add');
            Route::post('bank-loan-payment-save', 'Admin\BankLoanPaymentController@save')->name('bankLoanPayment.save');
            Route::get('bank-loan-payment-edit/{id}', 'Admin\BankLoanPaymentController@edit')->name('bankLoanPayment.edit');
            Route::post('bank-loan-payment-update', 'Admin\BankLoanPaymentController@update')->name('bankLoanPayment.update');
            Route::post('bank-loan-payment-delete', 'Admin\BankLoanPaymentController@delete')->name('bankLoanPayment.delete');

            Route::get('bank-loan-payment-schedule', 'Admin\BankLoanPaymentController@schedule')->name('bankLoanPayment.schedule');



            //Employee Commission
            Route::get('employee-commission', 'Admin\EmployeeCommissionController@index')->name('employeeCommission.index');
            Route::get('employee-commission-add', 'Admin\EmployeeCommissionController@add')->name('employeeCommission.add');
            Route::post('employee-commission-save', 'Admin\EmployeeCommissionController@save')->name('employeeCommission.save');
            Route::get('employee-commission-edit/{id}', 'Admin\EmployeeCommissionController@edit')->name('employeeCommission.edit');
            Route::post('employee-commission-update', 'Admin\EmployeeCommissionController@update')->name('employeeCommission.update');
            Route::post('employee-commission-delete', 'Admin\EmployeeCommissionController@delete')->name('employeeCommission.delete');


            // Staff Loan/Advance
            Route::get('staff-loan', 'Admin\StaffLoanController@index')->name('staffLoan.index');
            Route::get('staff-loan-add', 'Admin\StaffLoanController@add')->name('staffLoan.add');
            Route::post('staff-loan-save', 'Admin\StaffLoanController@save')->name('staffLoan.save');
            Route::get('staff-loan-edit/{id}', 'Admin\StaffLoanController@edit')->name('staffLoan.edit');
            Route::post('staff-loan-update', 'Admin\StaffLoanController@update')->name('staffLoan.update');
            Route::post('staff-loan-delete', 'Admin\StaffLoanController@delete')->name('staffLoan.delete');

            Route::get('staff-create-schedule', 'Admin\StaffLoanController@schedule')->name('staffLoan.schedule');


            // Staff Loan/Advance
            Route::get('staff-loan-collection', 'Admin\StaffLoanCollectionController@index')->name('staffLoanCollection.index');
            Route::get('staff-loan-collection-add', 'Admin\StaffLoanCollectionController@add')->name('staffLoanCollection.add');
            Route::post('staff-loan-collection-save', 'Admin\StaffLoanCollectionController@save')->name('staffLoanCollection.save');
            Route::get('staff-loan-collection-edit/{id}', 'Admin\StaffLoanCollectionController@edit')->name('staffLoanCollection.edit');
            Route::post('staff-loan-collection-update', 'Admin\StaffLoanCollectionController@update')->name('staffLoanCollection.update');
            Route::post('staff-loan-collection-delete', 'Admin\StaffLoanCollectionController@delete')->name('staffLoanCollection.delete');

            Route::get('staff-loan-collection-schedule', 'Admin\StaffLoanCollectionController@schedule')->name('staffLoanCollection.schedule');
        });
    });

    //Admin Login Url
    Route::get('/login', 'Auth\AdminLoginController@showLoginForm')->name('admin.login');
    Route::post('/login', 'Auth\AdminLoginController@login');
    Route::post('/logout', 'Auth\AdminLoginController@adminLogout')->name('admin.logout');

    // Password Reset Routes...
    Route::get('/password/reset', 'Auth\AdminForgotPasswordController@passwordForget')->name('admin.password.forget');
    Route::post('/password/email', 'Auth\AdminForgotPasswordController@passwordEmail')->name('admin.password.email');
    Route::get('/new-password/{email}', 'Auth\AdminForgotPasswordController@newPassword')->name('admin.password.newPassword');
    Route::post('/password/save', 'Auth\AdminForgotPasswordController@changePasswordSave')->name('admin.password.save');
});

Route::get('/clear', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('config:cache');
    Artisan::call('view:clear');
    Artisan::call('route:clear');

    return "Cleared!";
});

//Admin part end
