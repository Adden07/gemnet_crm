<?php

//auth routes for normal user

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Auth::routes(['verify' => false, 'register' => false, 'reset' => false]);

//admin auth routes
Route::prefix('web_admin')->namespace('Auth')->group(function () {
    Route::get('/login', 'AdminLoginController@showLoginForm')->name('admin.login');
    Route::post('/logout', 'AdminLoginController@logout')->name('admin.logout');
    Route::post('/login', 'AdminLoginController@login')->name('admin.login.submit');
});

    //crone jobs
    Route::prefix('crones')->namespace('Administrator')->name('crones.')->group(function(){
        Route::get('/user-expiry','CronController@userExpiry')->name('user_expiry');
        Route::get('/reset-qouta','CronController@resetQouta')->name('reset_qouta');
        Route::get('/qt_over','CronController@qtOver')->name('qt_over');
        Route::get('/queu','CronController@queue');
        
    });

//pages route
Route::namespace('Administrator')->middleware('auth:admin')->name('admin.')->group(function () {
    
    Route::get('/', 'HomeController@index')->name('home');
    
    //activity log routes
    Route::get('/activity-logs','ActivityLogController@index')->name('activity_logs.index');
    // Route::get('/activity-logs/search','ActivityLogController@search')->name('activity_logs.search');
    
    //admin routes
    Route::prefix('admin')->name('admins.')->group(function(){
        Route::get('/add','AdminController@add')->name('add');
        Route::post('/store','AdminController@store')->name('store');
        Route::get('/list','AdminController@index')->name('index');
        Route::get('/edit/{id}','AdminController@edit')->name('edit');
        Route::get('/profile/{id}','AdminController@details')->name('detail');
        Route::get('/check-unique','AdminController@checkUnique')->name('check_unique');
        Route::get('/remove-attachments','AdminController@removeAttachment')->name('remove_attachment');
        Route::post('/update-document','AdminController@updateDocument')->name('update_document');
        Route::post('/update-password','AdminController@updatePassword')->name('update_password');
        Route::post('/update-info','AdminController@updateInfo')->name('update_info');
    });

    //settings routes
    Route::prefix('settings')->name('settings.')->group(function(){
        Route::get('/','SettingController@index')->name('index');
        Route::post('/store','SettingController@store')->name('store');
        Route::post('/mode','SettingController@mode')->name('mode');
    });

    // //areas routes
    // Route::prefix('area')->name('areas.')->group(function(){
    //     Route::get('/','AreaController@index')->name('index');
    //     Route::post('/store','AreaController@store')->name('store');
    //     Route::get('/area-list/{id}','AreaController@areaList')->name('area_list');
    //     Route::get('/subarea-list/{id}', 'AreaController@subareaList')->name('sub_area_list');
    //     Route::get('/edit/{id}','AreaController@edit')->name('edit');
    //     Route::get('/delete/{id}','AreaController@delete')->name('delete');
    //     Route::get('/check-uniuqe-area-name','AreaController@checkUniqueAreaName')->name('check_unique_area_name');
    //     //subareas routes
    //     Route::post('/store-subarea','AreaController@storeSubarea')->name('store_subarea');
    //     Route::get('/check-unique-subarea','AreaController@checkUniqueSubarea')->name('check_unique_subarea');
    //     Route::get('/edit-subarea/{id}','AreaController@editSubarea')->name('edit_subarea');
    //     Route::get('/delete-subarea/{id}','AreaController@deleteSubarea')->name('delete_subarea');
    // }); 
        //areas routes
    //areas routes
    Route::prefix('area')->name('areas.')->group(function(){
        Route::get('/','AreaController@index')->name('index');
        Route::post('/store','AreaController@store')->name('store');
        Route::get('/area-list/{id}','AreaController@areaList')->name('area_list');
        Route::get('/edit/{id}','AreaController@edit')->name('edit');
        Route::get('/delete/{id}/{type}','AreaController@delete')->name('delete');
        Route::get('/check-uniuqe-area-name','AreaController@checkUniqueAreaName')->name('check_unique_area_name');
        //subareas routes
        Route::post('/store-subarea','AreaController@storeSubarea')->name('store_subarea');
        Route::get('/check-unique-subarea','AreaController@checkUniqueSubarea')->name('check_unique_subarea');
        Route::get('/edit-subarea/{id}','AreaController@editSubarea')->name('edit_subarea');
        Route::get('/delete-subarea/{id}','AreaController@deleteSubarea')->name('delete_subarea');
        Route::get('/subarea-list/{id}', 'AreaController@subareaList')->name('sub_area_list');

    }); 
        // //areas routes
    // Route::prefix('area')->name('areas.')->group(function(){
    //     Route::get('/','AreaController@index')->name('index');
    //     Route::post('/store','AreaController@store')->name('store');
    //     Route::get('/area-list/{id}','AreaController@areaList')->name('area_list');
    //     Route::get('/subarea-list/{id}', 'AreaController@subareaList')->name('sub_area_list');
    //     Route::get('/edit/{id}','AreaController@edit')->name('edit');
    //     Route::get('/delete/{id}','AreaController@delete')->name('delete');
    //     Route::get('/check-uniuqe-area-name','AreaController@checkUniqueAreaName')->name('check_unique_area_name');
    //     //subareas routes
    //     Route::post('/store-subarea','AreaController@storeSubarea')->name('store_subarea');
    //     Route::get('/check-unique-subarea','AreaController@checkUniqueSubarea')->name('check_unique_subarea');
    //     Route::get('/edit-subarea/{id}','AreaController@editSubarea')->name('edit_subarea');
    //     Route::get('/delete-subarea/{id}','AreaController@deleteSubarea')->name('delete_subarea');
    // }); 

    //city routes
    Route::prefix('city')->name('cities.')->group(function(){
        Route::post('/store','CityController@store')->name('store');
        Route::get('/check-unique-name','CityController@checkUniqueName')->name('check_unique_name');
        Route::get('/ediy/{id}','CityController@edit')->name('edit');
        Route::get('/delete/{id}','CityController@delete')->name('delete');
    });
    //customize routes
    Route::prefix('customizes')->name('customizes.')->group(function(){
        Route::get('/','CustomizeController@index')->name('index');
        Route::post('/store','CustomizeController@store')->name('store');    
        Route::get('/reset/{id?}','CustomizeController@reset')->name('reset');
    });

    //user routes
    Route::prefix('users')->name('users.')->group(function(){
        Route::get('/all','UserController@index')->name('index');
        Route::get('/add','UserController@add')->name('add');
        Route::post('/store','UserController@store')->name('store');
        Route::get('/edit/{id}','UserController@edit')->name('edit');
        Route::get('/check-unique','UserController@checkUnique')->name('check_unique');
        Route::get('/details/{id}','UserController@details')->name('detail');
        Route::get('/remove-attachments','UserController@removeAttachment')->name('remove_attachment');
        Route::get('/subareas/{area_id}','UserController@subareas')->name('subareas');
        Route::get('/profile/{id}','UserController@profile')->name('profile');
        Route::post('/update-password','UserController@updatePassword')->name('update_password');
        Route::post('/update-documents','UserController@updateDocument')->name('update_document');
        Route::post('/update-info','UserController@updateInfo')->name('update_info');
        Route::get('/online-users','UserController@onlineUsers')->name('online_user');
        Route::get('/offline-users','UserController@oflineUsers')->name('ofline_user');
        Route::get('/login-fail-logs','UserController@loginFailLogs')->name('login_fail_log');
        Route::get('/mac-vendor-user','UserController@macVendorUsers')->name('mac_vendor_user');
        Route::get('/kick/{id}','UserController@kickUser')->name('kick');
        Route::get('/change-user-status/{id}/{status}','UserController@changeStatus')->name('change_status');
        Route::get('/remove-mac/{id}','UserController@removeMac')->name('remove_mac');
        Route::get('/reset-qouta/{id}','UserController@resetQouta')->name('reset_qouta');
        Route::get('/remarks','UserController@remarks')->name('remarks');
        Route::get('/login-details', 'UserController@loginDetail')->name('login_detail');
        Route::get('/search-user', 'UserController@userSearch')->name('search');
        Route::get('/get-package-count', 'UserController@getPackageCount')->name('get_pacakge_count');
        //update user routes
        Route::get('/update-users', 'UserController@updateUsers')->name('update_users');
        Route::post('/export-update-users', 'UserController@exportUpdateUsers')->name('export_update_users');
        Route::post('/update-users-import-excel', 'UserController@importUpdateUserExcel')->name('import_update_user_excel');
        Route::get('/update-expiration/{user_id}', 'UserController@updateExpiration')->name('update_expiration');
        //update expiration route
        Route::get('/update-users-expiration', 'UserController@updateUsersExpiration')->name('update_users_expiration');
        Route::post('/update-users-expiration-import-excel', 'UserController@importUpdateUserExpirationExcel')->name('import_update_user_expiration_excel');
        Route::post('/validate-update-users-expiration', 'UserController@validateUpdateUserExpiration')->name('validate_update_users_expiration');
        Route::post('/export-udpate-users-expiration', 'UserController@exportUpdateUserExpiration')->name('export_update_user_expiration');
        Route::get('/update-user-expiration-modal/{id}', 'UserController@updateUserExpirationModal')->name('update_user_expiration_modal');
        Route::post('/update-user-expiraton', 'UserController@updateUserExpiration')->name('update_user_expiration');
        Route::get('/check-user-expiration-task-status/{id}', 'UserController@checkUpdateUserExpirationStatus')->name('update_user_expiration_task_status');
        Route::post('/delete-update-users-expiration', 'UserController@deleteImportUserExpiration')->name('delete_update_user_expiration');
        Route::post('/migrate-update-user-expiration', 'UserController@migrateUpdateUserExpiration')->name('migrate_update_user_expiration');
        Route::post('/delete-multiple-update-user-expiration', 'UserController@deleteMultipleUpdateUserExpiration')->name('delete_multiple_update_user_expiration');
        Route::get('/update-users-expiraiton-history', 'UserController@updateUsersExpirationHistory' )->name('update_users_expiration_history');
        Route::get('/update-users-expiraiton-task-history/{task_id}', 'UserController@updateUsersExpirationTaskHistory' )->name('update_users_expiration_task_history');
        //new routes
        Route::get('/get-user-current-balance/{id}', 'UserController@getUserCurrentBalance')->name('get_user_current_balance');
    });

    //package routes
    Route::prefix('package')->name('packages.')->group(function(){
        Route::get('/add-user-package/{id}','PackageController@addUserPackage')->name('add_user_package');
        Route::get('/update-user-package','PackageController@updateUserPackage')->name('update_user_package');
        Route::get('/change-user-package','PackageController@changeUserPackage')->name('change_user_package');
        Route::get('/upgrade-user-package-modal/{id}','PackageController@upgradeUserPackageModal')->name('upgrade_user_package_modal');
        Route::get('/upgrade-user-package','PackageController@upgradeUserPackage')->name('upgrade_user_package');
        Route::get('/get-packages/{user_type}', 'PackageController@getPackages')->name('get_packages');
        Route::get('/create-expiration-date/{package_id}/{user_id}', 'PackageController@createExpirationDate')->name('create_expiration_date');
    });

    //permisson type routes
    Route::prefix('permission-types')->name('permissions.')->group(function(){
        Route::get('/add','PermissionController@addType')->name('add_type');
        Route::post('/store','PermissionController@storeType')->name('store_type');
        Route::get('/permission','PermissionController@addPermission')->name('add_permission');
        Route::post('/permission-store','PermissionController@storePermission')->name('store_permisssion');

    });

    //user role permissions
    Route::prefix('user-role-permissions')->name('role_permissions.')->group(function(){
        Route::get('/add','UserRolePermissionController@add')->name('add');
        Route::post('/store','UserRolePermissionController@store')->name('store');
        Route::get('/','UserRolePermissionController@index')->name('index');
        Route::get('/edit/{id}','UserRolePermissionController@edit')->name('edit');
    });
    

    //accounts
    Route::prefix('accounts')->name('accounts.')->group(function(){
        //invoices routes
        Route::prefix('invoices')->name('invoices.')->group(function(){
            Route::get('/','InvoiceController@index')->name('index');
            Route::get('/subdealers/{id}','InvoiceController@getSubdealers')->name('get_subdealers');
            Route::get('/pay-invoice/{id}','InvoiceController@payInvoice')->name('pay_invoice');
            Route::get('/unpaid-invoices','InvoiceController@unpaidInvoice')->name('unpaid_invoice');

        });
        //payments routes
        Route::prefix('payments')->name('payments.')->group(function(){
            Route::get('/','PaymentController@index')->name('index');
            Route::get('/add','PaymentController@add')->name('add');
            Route::post('/store','PaymentController@store')->name('store');
            Route::get('/edit/{id}','PaymentController@edit')->name('edit');
            Route::get('/available-balance/{id}','PaymentController@getBalance')->name('balance');
            Route::get('/delete/{id}', 'PaymentController@delete')->name('delete');
            Route::get('/approve-payments', 'PaymentController@approvePayments')->name('approve_payments');
            Route::get('/approve-payment/{id}', 'PaymentController@approvePayment')->name('approve_payment');

        });

        Route::prefix('transactions')->name('transactions.')->group(function(){
            Route::get('/','TransactionController@index')->name('index');
        });
    });

    //profile routes
    Route::prefix('profile')->name('profiles.')->group(function(){
        Route::get('/','ProfileController@index')->name('index');
        Route::post('/update-password','ProfileController@updatePassword')->name('update_password');
        Route::get('/disable-user/{id}','ProfileController@disableUser')->name('disable_user');
        Route::get('/enable-user/{id}','ProfileController@EnableUser')->name('enable_user');
        //update franchise network credit limit
        Route::post('/update-credit-limit', 'ProfileController@updateCreditLimit')->name('credit_limit');
        Route::get('/set-permission-limit/{id}/{limit}', 'ProfileController@setPermissionLimit')->name('permission_limit');
    }); 
    
    //admin acl roues
    Route::prefix('acl')->name('acls.')->group(function(){
        Route::get('/', 'AclController@index')->name('index');
        Route::post('/store', 'AclController@store')->name('store');
    });

    //migrations
    Route::prefix('migrations')->name('migrations.')->group(function(){
        Route::get('/','MigrationController@index')->name('index');
    });

    //staff routes
    Route::prefix('staff')->name('staffs.')->group(function(){
        Route::get('/','StaffController@index')->name('index');
        Route::get('/add','StaffController@add')->name('add');
        Route::post('/store','StaffController@store')->name('store');
    });

     //notifications
    Route::get('/notifications', 'NotificationsController@index')->name('notifications');
    Route::post('/notifications/clear/all', 'NotificationsController@clear')->name('notifications.clear_all');
    Route::get('/notifications/clear/{id}', 'NotificationsController@clear')->name('notifications.clear');
    Route::get('/notifications/delete/{id}', 'NotificationsController@delete')->name('notifications.delete');

    //profile pages
    Route::get('/update-profile', 'StaffController@update_profile')->name('update_profile');
    Route::post('/save-profile', 'StaffController@save_profile')->name('save_profile');
    Route::post('/change-password', 'StaffController@change_password')->name('change_password');

    //Shipments routes
    // Route::get('/shipments/marketplace/{status?}', 'ShipmentController@index')->name('shipments');
    // Route::get('/shipments/search', 'ShipmentController@search_shipment')->name('shipments.search');
    // Route::get('/shipments/get_contact_details', 'ShipmentController@get_contact_details')->name('shipments.get_contact_details');
    // Route::get('/shipments/instant/{status?}', 'ShipmentController@instant_shipments')->name('shipments.instant');
    // Route::get('/shipments/premium/{status?}', 'ShipmentController@premium_shipments')->name('shipments.premium');
    // Route::get('/shipments/view/{shipment_no}', 'ShipmentController@view')->name('shipments.view');
    // Route::post('/shipments/view_quote_details', 'ShipmentController@get_quote_details')->name('shipments.quote_details');
    // Route::post('/shipments/save', 'ShipmentController@save')->name('shipments.save');
    // Route::get('/shipments/delete/{shipment_no}', 'ShipmentController@delete')->name('shipments.delete');
    // Route::post('/shipments/update_info', 'ShipmentController@update_info')->name('shipments.update_info');
    // Route::post('/shipments/upload_document', 'ShipmentController@upload_document')->name('shipments.upload_document');
    // Route::post('/shipments/update_status', 'ShipmentController@update_status')->name('shipments.update_status');
    // Route::get('/shipments/delete_document/{id}', 'ShipmentController@delete_document')->name('shipments.delete_document');

    //Permission Type routes
    Route::get('/permission-types', 'PermissionTypeController@index')->name('permission_types');
    Route::post('/permission-types/save', 'PermissionTypeController@save')->name('permission_types.save');
    Route::get('/permission-types/delete/{type_id}', 'PermissionTypeController@delete')->name('permission_types.delete');
});

Route::get('/errors/{method}', 'ErrorController@index');
