<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function(){
   return redirect()->route('login');
});

// API
Route::group([
        'prefix' => "api/"
    ], function (){
        // AUTH
        Route::post('/tokens/create', 'ApiController@getToken');
        Route::middleware('auth:sanctum')->post('/tokens/revoke', 'ApiController@revokeToken');

        Route::middleware('auth:sanctum')->post('reading/device', 'ApiController@receive');
        Route::middleware('auth:sanctum')->post('reading/getDevices', 'ApiController@getDevices');
    }
);

Route::group([
        'middleware' => 'auth',
    ], function() {

        Route::get('dashboard', 'DashboardController@index')
            ->defaults('sidebar', 1)
            ->defaults('icon', 'fas fa-list')
            ->defaults('name', 'Dashboard')
            ->defaults('roles', array('Admin', 'RHU'))
            ->name('dashboard')
            ->defaults('href', 'dashboard');

        // REQUEST ROUTES
        // $cname = "request";
        // Route::group([
        //         'as' => "$cname.",
        //         'prefix' => "$cname/"
        //     ], function () use($cname){
        //         Route::get("/", ucfirst($cname) . "Controller@index")
        //             ->defaults("sidebar", 1)
        //             ->defaults("icon", "fa-light fa-keyboard")
        //             ->defaults("name", "Requesition Entry")
        //             ->defaults("roles", array("Admin", "RHU", "Approver"))
        //             ->name($cname)
        //             ->defaults("href", "/$cname");

        //         Route::get("get/", ucfirst($cname) . "Controller@get")->name('get');
        //         Route::get("getPendingRequests/", ucfirst($cname) . "Controller@getPendingRequests")->name('getPendingRequests');
        //         Route::get("create/", ucfirst($cname) . "Controller@create")->name('create');
        //         Route::post("store/", ucfirst($cname) . "Controller@store")->name('store');
        //         Route::post("update/", ucfirst($cname) . "Controller@update")->name('update');
        //         Route::post("delete/", ucfirst($cname) . "Controller@delete")->name('delete');

        //         Route::get("inputInfo", ucfirst($cname) . "Controller@inputInfo")->name('inputInfo');
        //         Route::get("getNewAlerts", ucfirst($cname) . "Controller@getNewAlerts")->name('getNewAlerts');
        //         Route::get("seenNewAlerts", ucfirst($cname) . "Controller@seenNewAlerts")->name('seenNewAlerts');
        //         Route::get("getAdminAlert/", ucfirst($cname) . "Controller@getAdminAlert")->name('getAdminAlert');
        //     }
        // );

        // DATA ROUTES
        // $cname = "data";
        // Route::group([
        //         'as' => "$cname.",
        //         'prefix' => "$cname/"
        //     ], function () use($cname){
        //         Route::get("/", ucfirst($cname) . "Controller@index")
        //             ->defaults("sidebar", 1)
        //             ->defaults("icon", "fa-solid fa-keyboard")
        //             ->defaults("name", "Data Entry")
        //             ->defaults("roles", array("Admin", "RHU"))
        //             ->name($cname)
        //             ->defaults("href", "/$cname");

        //         Route::get("get/", ucfirst($cname) . "Controller@get")->name('get');
        //         Route::post("store/", ucfirst($cname) . "Controller@store")->name('store');
        //         Route::post("update/", ucfirst($cname) . "Controller@update")->name('update');
        //         Route::post("delete/", ucfirst($cname) . "Controller@delete")->name('delete');
        //     }
        // );

        // RECEIVE ROUTE
        // $cname = "request";
        // Route::group([
        //         'as' => "$cname.",
        //         'prefix' => "$cname/"
        //     ], function () use($cname){
        //         Route::get("receive/", ucfirst($cname) . "Controller@receive")
        //             ->defaults("sidebar", 1)
        //             ->defaults("icon", "fa-solid fa-handshake-simple")
        //             ->defaults("name", "Receive")
        //             ->defaults("roles", array("RHU"))
        //             ->name('receive')
        //             ->defaults("href", "/$cname/receive");
        //     }
        // );

        // RX ROUTES -> READING
        $cname = "reading";
        Route::group([
                'as' => "$cname.",
                'prefix' => "$cname/"
            ], function () use($cname){
                Route::get("/", ucfirst($cname) . "Controller@index")
                    ->defaults("sidebar", 1)
                    ->defaults("icon", "fas fa-pen-to-square")
                    ->defaults("name", "Reading")
                    ->defaults("roles", array("Admin", 'RHU'))
                    ->name($cname)
                    ->defaults("href", "/$cname");

                Route::get("get/", ucfirst($cname) . "Controller@get")->name('get');
                Route::get("getReading/", ucfirst($cname) . "Controller@getReading")->name('getReading');
                Route::get("getLatestReading/", ucfirst($cname) . "Controller@getLatestReading")->name('getLatestReading');
                Route::get("perBuilding/", ucfirst($cname) . "Controller@perBuilding")->name('perBuilding');
                Route::get("exportPerBuilding/", ucfirst($cname) . "Controller@exportPerBuilding")->name('exportPerBuilding');
                Route::get("moxaPerBuilding/", ucfirst($cname) . "Controller@moxaPerBuilding")->name('moxaPerBuilding');
                Route::get("perBuilding2/", ucfirst($cname) . "Controller@perBuilding2")->name('perBuilding2');
                Route::get("moxaPerBuilding2/", ucfirst($cname) . "Controller@moxaPerBuilding2")->name('moxaPerBuilding2');
                Route::post("store/", ucfirst($cname) . "Controller@store")->name('store');
                Route::post("update/", ucfirst($cname) . "Controller@update")->name('update');
                Route::post("delete/", ucfirst($cname) . "Controller@delete")->name('delete');
            }
        );

        // BILLING
        $cname = "billing";
        Route::group([
                'as' => "$cname.",
                'prefix' => "$cname/"
            ], function () use($cname){
                Route::get("/", ucfirst($cname) . "Controller@index")
                    ->defaults("sidebar", 1)
                    ->defaults("icon", "fas fa-file-invoice-dollar")
                    ->defaults("name", "Billing")
                    ->defaults("roles", array("Admin"))
                    ->name($cname)
                    ->defaults("href", "/$cname");

                Route::get("get/", ucfirst($cname) . "Controller@get")->name('get');
                Route::post("store/", ucfirst($cname) . "Controller@store")->name('store');
                Route::post("update/", ucfirst($cname) . "Controller@update")->name('update');
                Route::post("delete/", ucfirst($cname) . "Controller@delete")->name('delete');
                Route::post("pay/", ucfirst($cname) . "Controller@pay")->name('pay');
            }
        );

        // USER ROUTES
        $cname = "user";
        Route::group([
                'as' => "$cname.",
                'prefix' => "$cname/"
            ], function () use($cname){
                Route::get("get/", ucfirst($cname) . "Controller@get")->name('get');
                Route::post("store/", ucfirst($cname) . "Controller@store")->name('store');
                Route::post("update/", ucfirst($cname) . "Controller@update")->name('update');
                Route::post("delete/", ucfirst($cname) . "Controller@delete")->name('delete');
                Route::post("restore/", ucfirst($cname) . "Controller@restore")->name('restore');
                Route::post("updatePassword/", ucfirst($cname) . "Controller@updatePassword")->name('updatePassword');
            }
        );

        // ADMIN ROUTES
        $cname = "admin";
        Route::group([
                'as' => "$cname.",
                'prefix' => "$cname/"
            ], function () use($cname){

                Route::get("/", ucfirst($cname) . "Controller@index")
                    ->defaults("sidebar", 1)
                    ->defaults("icon", "fas fa-users")
                    ->defaults("name", "Admin Management")
                    ->defaults("roles", array("Super Admin"))
                    ->name($cname)
                    ->defaults("href", "/$cname");

                Route::get("get/", ucfirst($cname) . "Controller@get")->name('get');
                Route::post("store/", ucfirst($cname) . "Controller@store")->name('store');
                Route::post("update/", ucfirst($cname) . "Controller@update")->name('update');
                Route::post("delete/", ucfirst($cname) . "Controller@delete")->name('delete');
            }
        );

        // RHU ROUTES
        $cname = "site";
        Route::group([
                'as' => "$cname.",
                'prefix' => "$cname/"
            ], function () use($cname){

                Route::get("/", ucfirst($cname) . "Controller@index")
                    ->defaults("sidebar", 1)
                    ->defaults("icon", "fas fa-map-location")
                    ->defaults("name", "Site")
                    ->defaults("roles", array("Admin"))
                    ->defaults("group", "Settings")
                    ->name($cname)
                    ->defaults("href", "/$cname");

                Route::get("get/", ucfirst($cname) . "Controller@get")->name('get');
                Route::post("store/", ucfirst($cname) . "Controller@store")->name('store');
                Route::post("update/", ucfirst($cname) . "Controller@update")->name('update');
                Route::post("delete/", ucfirst($cname) . "Controller@delete")->name('delete');
            }
        );

        // DEVICE
        $cname = "device";
        Route::group([
                'as' => "$cname.",
                'prefix' => "$cname/"
            ], function () use($cname){

                // Route::get("/", ucfirst($cname) . "Controller@index")
                //     ->defaults("sidebar", 1)
                //     ->defaults("icon", "fas fa-building")
                //     ->defaults("name", "Building")
                //     ->defaults("roles", array("Admin"))
                //     ->defaults("group", "Settings")
                //     ->name($cname)
                //     ->defaults("href", "/$cname");

                Route::get("get/", ucfirst($cname) . "Controller@get")->name('get');
                Route::post("store/", ucfirst($cname) . "Controller@store")->name('store');
                Route::post("update/", ucfirst($cname) . "Controller@update")->name('update');
                Route::post("delete/", ucfirst($cname) . "Controller@delete")->name('delete');
            }
        );

        // BHC ROUTES
        // $cname = "bhc";
        // Route::group([
        //         'as' => "$cname.",
        //         'prefix' => "$cname/"
        //     ], function () use($cname){

        //         Route::get("/", ucfirst($cname) . "Controller@index")
        //             ->defaults("sidebar", 1)
        //             ->defaults("icon", "fas fa-clinic-medical")
        //             ->defaults("name", "Barangay Health Center")
        //             ->defaults("roles", array("Admin", "RHU"))
        //             ->defaults("group", "Settings")
        //             ->name($cname)
        //             ->defaults("href", "/$cname");

        //         Route::get("get/", ucfirst($cname) . "Controller@get")->name('get');
        //         Route::get("get2/", ucfirst($cname) . "Controller@get2")->name('get2');
        //         Route::post("store/", ucfirst($cname) . "Controller@store")->name('store');
        //         Route::post("update/", ucfirst($cname) . "Controller@update")->name('update');
        //         Route::post("delete/", ucfirst($cname) . "Controller@delete")->name('delete');
        //     }
        // );

        // SKU ROUTES
        $cname = "building";
        Route::group([
                'as' => "$cname.",
                'prefix' => "$cname/"
            ], function () use($cname){

                Route::get("/", ucfirst($cname) . "Controller@index")
                    ->defaults("sidebar", 1)
                    ->defaults("icon", "fa-solid fa-building")
                    ->defaults("name", "Area")
                    ->defaults("roles", array("Admin", "RHU"))
                    ->defaults("group", "Settings")
                    ->name($cname)
                    ->defaults("href", "/$cname");

                Route::get("get/", ucfirst($cname) . "Controller@get")->name('get');
                Route::get("getCategories/", ucfirst($cname) . "Controller@getCategories")->name('getCategories');
                Route::get("getReorder/", ucfirst($cname) . "Controller@getReorder")->name('getReorder');
                Route::post("store/", ucfirst($cname) . "Controller@store")->name('store');
                Route::post("storeCategory/", ucfirst($cname) . "Controller@storeCategory")->name('storeCategory');
                Route::post("update/", ucfirst($cname) . "Controller@update")->name('update');
                Route::post("updateCategory/", ucfirst($cname) . "Controller@updateCategory")->name('updateCategory');
                Route::post("updateReorder/", ucfirst($cname) . "Controller@updateReorder")->name('updateReorder');
                Route::post("delete/", ucfirst($cname) . "Controller@delete")->name('delete');
                Route::post("deleteReorder/", ucfirst($cname) . "Controller@deleteReorder")->name('deleteReorder');
                Route::post("deleteCategory/", ucfirst($cname) . "Controller@deleteCategory")->name('deleteCategory');

                Route::get("assign/", ucfirst($cname) . "Controller@assign")->name('assign');
            }
        );

        // APPROVER ROUTES -> DEVICES
        $cname = "device";
        Route::group([
                'as' => "$cname.",
                'prefix' => "$cname/"
            ], function () use($cname){
                Route::get("/", ucfirst($cname) . "Controller@index")
                    ->defaults("sidebar", 1)
                    ->defaults("icon", "fa-solid fa-microchip")
                    ->defaults("name", "Device")
                    ->defaults("roles", array("Admin", "RHU"))
                    ->defaults("group", "Settings")
                    ->name($cname)
                    ->defaults("href", "/$cname");

                Route::get("get/", ucfirst($cname) . "Controller@get")->name('get');
                Route::post("store/", ucfirst($cname) . "Controller@store")->name('store');
                Route::post("update/", ucfirst($cname) . "Controller@update")->name('update');
                Route::post("delete/", ucfirst($cname) . "Controller@delete")->name('delete');
            }
        );

        // STOCK ROUTES
        // $cname = "stock";
        // Route::group([
        //         'as' => "$cname.",
        //         'prefix' => "$cname/"
        //     ], function () use($cname){

        //         Route::get("get/", ucfirst($cname) . "Controller@get")->name('get');
        //     }
        // );

        // TRANSACTION TYPE ROUTES
        $cname = "transactionType";
        Route::group([
                'as' => "$cname.",
                'prefix' => "$cname/"
            ], function () use($cname){
                Route::get("/", ucfirst($cname) . "Controller@index")
                    ->defaults("sidebar", 1)
                    ->defaults("icon", "fa-solid fa-tags")
                    ->defaults("name", "Classifications")
                    ->defaults("roles", array("Admin"))
                    ->defaults("group", "Settings")
                    ->name($cname)
                    ->defaults("href", "/$cname");

                Route::get("get/", ucfirst($cname) . "Controller@get")->name('get');
                Route::post("store/", ucfirst($cname) . "Controller@store")->name('store');
                Route::post("update/", ucfirst($cname) . "Controller@update")->name('update');
                Route::post("delete/", ucfirst($cname) . "Controller@delete")->name('delete');
            }
        );

        $cname = "subscriber";
        Route::group([
                'as' => "$cname.",
                'prefix' => "$cname/"
            ], function () use($cname){
                Route::get("/", ucfirst($cname) . "Controller@index")
                    ->defaults("sidebar", 1)
                    ->defaults("icon", "fa-solid fa-bell")
                    ->defaults("name", "Subscribers")
                    ->defaults("roles", array("Admin"))
                    ->defaults("group", "Settings")
                    ->name($cname)
                    ->defaults("href", "/$cname");

                Route::get("get/", ucfirst($cname) . "Controller@get")->name('get');
                Route::post("store/", ucfirst($cname) . "Controller@store")->name('store');
                Route::post("update/", ucfirst($cname) . "Controller@update")->name('update');
                Route::post("delete/", ucfirst($cname) . "Controller@delete")->name('delete');
            }
        );

        // LOCATION ROUTES
        // $cname = "location";
        // Route::group([
        //         'as' => "$cname.",
        //         'prefix' => "$cname/"
        //     ], function () use($cname){
        //         Route::get("/", ucfirst($cname) . "Controller@index")
        //             ->defaults("sidebar", 1)
        //             ->defaults("icon", "fa-light fa-location-dot")
        //             ->defaults("name", "Locations")
        //             ->defaults("roles", array("Admin"))
        //             ->defaults("group", "Settings")
        //             ->name($cname)
        //             ->defaults("href", "/$cname");

        //         Route::get("get/", ucfirst($cname) . "Controller@get")->name('get');
        //         Route::post("store/", ucfirst($cname) . "Controller@store")->name('store');
        //         Route::post("update/", ucfirst($cname) . "Controller@update")->name('update');
        //         Route::post("delete/", ucfirst($cname) . "Controller@delete")->name('delete');
        //     }
        // );

        // REPORT ROUTES
        // $cname = "report";
        // Route::group([
        //         'as' => "$cname.",
        //         'prefix' => "$cname/"
        //     ], function () use($cname){

        //         // INVENTORY REPORT
        //         // INVENTORY REPORT
        //         // INVENTORY REPORT
        //         Route::get("inventory/", ucfirst($cname) . "Controller@inventory")
        //             ->defaults("sidebar", 1)
        //             ->defaults("icon", "fa-solid fa-box-circle-check")
        //             ->defaults("name", "Inventory")
        //             ->defaults("roles", array("Admin", "RHU"))
        //             ->defaults("group", "Reports")
        //             ->name('inventory')
        //             ->defaults("href", "/$cname/inventory");

        //         Route::get("getInventory/", ucfirst($cname) . "Controller@getInventory")->name('getInventory');

        //         // SALES REPORT
        //         // SALES REPORT
        //         // SALES REPORT
        //         Route::get("sales/", ucfirst($cname) . "Controller@sales")
        //             ->defaults("sidebar", 1)
        //             ->defaults("icon", "fa-solid fa-dollar-sign")
        //             ->defaults("name", "Sales")
        //             ->defaults("roles", array("Admin", "RHU"))
        //             ->defaults("group", "Reports")
        //             ->name('sales')
        //             ->defaults("href", "/$cname/sales");

        //         Route::get("getSales/", ucfirst($cname) . "Controller@getSales")->name('getSales');

        //         // PURCHASE ORDER REPORT
        //         // PURCHASE ORDER REPORT
        //         // PURCHASE ORDER REPORT
        //         Route::get("purchaseOrder/", ucfirst($cname) . "Controller@purchaseOrder")
        //             ->defaults("sidebar", 1)
        //             ->defaults("icon", "fa-solid fa-hand-holding-dollar")
        //             ->defaults("name", "Purchase Order")
        //             ->defaults("roles", array("Admin", "RHU"))
        //             ->defaults("group", "Reports")
        //             ->name('purchaseOrder')
        //             ->defaults("href", "/$cname/purchaseOrder");

        //         Route::get("getPurchaseOrder/", ucfirst($cname) . "Controller@getPurchaseOrder")->name('getPurchaseOrder');

        //         // DAILY SHEETS REPORT
        //         // DAILY SHEETS REPORT
        //         // DAILY SHEETS REPORT
        //         Route::get("dailySheet/", ucfirst($cname) . "Controller@dailySheet")
        //             ->defaults("sidebar", 1)
        //             ->defaults("icon", "fa-solid fa-files")
        //             ->defaults("name", "Daily Sheets")
        //             ->defaults("roles", array("Admin", "RHU"))
        //             ->defaults("group", "Reports")
        //             ->name('dailySheet')
        //             ->defaults("href", "/$cname/dailySheet");

        //         Route::get("getDailySheet/", ucfirst($cname) . "Controller@getDailySheet")->name('getDailySheet');

        //         // BIN CARD REPORT
        //         // BIN CARD REPORT
        //         // BIN CARD REPORT
        //         Route::get("binCard/", ucfirst($cname) . "Controller@binCard")
        //             ->defaults("sidebar", 1)
        //             ->defaults("icon", "fa-solid fa-cards-blank")
        //             ->defaults("name", "Bin Card")
        //             ->defaults("roles", array("Admin", "RHU"))
        //             ->defaults("group", "Reports")
        //             ->name('binCard')
        //             ->defaults("href", "/$cname/binCard");

        //         Route::get("getBinCard/", ucfirst($cname) . "Controller@getBinCard")->name('getBinCard');

        //         // DISPOSED TO RHU
        //         // DISPOSED TO RHU
        //         // DISPOSED TO RHU
        //         Route::get("toRhu/", ucfirst($cname) . "Controller@toRhu")
        //             ->defaults("sidebar", 1)
        //             ->defaults("icon", "fa-solid fa-right-left")
        //             ->defaults("name", "Transferred to RHU")
        //             ->defaults("roles", array("Admin"))
        //             ->defaults("group", "Reports")
        //             ->name('toRhu')
        //             ->defaults("href", "/$cname/toRhu");

        //         Route::get("getToRhu/", ucfirst($cname) . "Controller@getToRhu")->name('getToRhu');

        //         // DISPOSED TO BARANGAY
        //         // DISPOSED TO BARANGAY
        //         // DISPOSED TO BARANGAY
        //         Route::get("toBarangay/", ucfirst($cname) . "Controller@toBarangay")
        //             ->defaults("sidebar", 1)
        //             ->defaults("icon", "fa-solid fa-right-left")
        //             ->defaults("name", "Transferred to Barangay")
        //             ->defaults("roles", array("Admin", "RHU"))
        //             ->defaults("group", "Reports")
        //             ->name('toBarangay')
        //             ->defaults("href", "/$cname/toBarangay");

        //         Route::get("getToBarangay/", ucfirst($cname) . "Controller@getToBarangay")->name('getToBarangay');

        //         // WASTED MEDICINES
        //         Route::get("wastedMedicine/", ucfirst($cname) . "Controller@wastedMedicine")
        //             ->defaults("sidebar", 1)
        //             ->defaults("icon", "fa-solid fa-dumpster")
        //             ->defaults("name", "Wasted Medicinces")
        //             ->defaults("roles", array("Admin", "RHU"))
        //             ->defaults("group", "Reports")
        //             ->name('wastedMedicine')
        //             ->defaults("href", "/$cname/wastedMedicine");

        //         Route::get("getWastedMedicine/", ucfirst($cname) . "Controller@getWastedMedicine")->name('getWastedMedicine');

        //         // ALERT REPORT
        //         // ALERT REPORT
        //         // ALERT REPORT
        //         Route::get("alert/", ucfirst($cname) . "Controller@alert")
        //             ->defaults("sidebar", 1)
        //             ->defaults("icon", "fa-solid fa-bell-exclamation")
        //             ->defaults("name", "Alerts")
        //             ->defaults("roles", array("Admin"))
        //             ->defaults("group", "Reports")
        //             ->name('alert')
        //             ->defaults("href", "/$cname/alert");

        //         Route::get("getAlert/", ucfirst($cname) . "Controller@getAlert")->name('getAlert');

        //         // DASHBOARD CHARTS
        //         Route::get("salesPerRhu/", ucfirst($cname) . "Controller@salesPerRhu")->name('salesPerRhu');
        //         Route::get("deliveredRequests/", ucfirst($cname) . "Controller@deliveredRequests")->name('deliveredRequests');
        //     }
        // );


        // EXPORT
        // EXPORT
        // EXPORT
        // $cname = "export";
        // Route::group([
        //         'as' => "$cname.",
        //         'prefix' => "$cname/"
        //     ], function () use($cname){
        //         Route::get($cname . "BinCard/", ucfirst($cname) . "Controller@$cname" . "BinCard")->name($cname . "BinCard");
        //         Route::get($cname . "Inventory/", ucfirst($cname) . "Controller@$cname" . "Inventory")->name($cname . "Inventory");
        //         Route::get($cname . "Sales/", ucfirst($cname) . "Controller@$cname" . "Sales")->name($cname . "Sales");
        //         Route::get($cname . "PurchaseOrder/", ucfirst($cname) . "Controller@$cname" . "PurchaseOrder")->name($cname . "PurchaseOrder");
        //         Route::get($cname . "DailySheet/", ucfirst($cname) . "Controller@$cname" . "DailySheet")->name($cname . "DailySheet");
        //         Route::get($cname . "Requests/", ucfirst($cname) . "Controller@$cname" . "Requests")->name($cname . "Requests");
        //         Route::get($cname . "Sku/", ucfirst($cname) . "Controller@$cname" . "Sku")->name($cname . "Sku");
        //     }
        // );

        // LOCATION ROUTES
        $cname = "theme";
        Route::group([
                'as' => "$cname.",
                'prefix' => "$cname/"
            ], function () use($cname){
                Route::get("get/", ucfirst($cname) . "Controller@get")->name('get');
                Route::post("update/", ucfirst($cname) . "Controller@update")->name('update');
            }
        );

        // DATATABLES
        $cname = "datatable";
        Route::group([
                'as' => "$cname.",
                'prefix' => "$cname/"
            ], function () use($cname){

                Route::get("admin", ucfirst($cname) . "Controller@admin")->name('admin');
                Route::get("rhu", ucfirst($cname) . "Controller@rhu")->name('rhu');
                Route::get("moxa", ucfirst($cname) . "Controller@moxa")->name('moxa');
                Route::get("category", ucfirst($cname) . "Controller@category")->name('category');
                Route::get("bhc", ucfirst($cname) . "Controller@bhc")->name('bhc');
                Route::get("medicine", ucfirst($cname) . "Controller@medicine")->name('medicine');
                Route::get("medicine2", ucfirst($cname) . "Controller@medicine2")->name('medicine2');
                Route::get("transactionType", ucfirst($cname) . "Controller@transactionType")->name('transactionType');
                Route::get("approver", ucfirst($cname) . "Controller@approver")->name('approver');
                Route::get("reading", ucfirst($cname) . "Controller@reading")->name('reading');
                Route::get("requests", ucfirst($cname) . "Controller@requests")->name('requests');
                Route::get("receive", ucfirst($cname) . "Controller@receive")->name('receive');
                Route::get("rx", ucfirst($cname) . "Controller@rx")->name('rx');
                Route::get("site", ucfirst($cname) . "Controller@site")->name('site');
                Route::get("data", ucfirst($cname) . "Controller@data")->name('data');
                Route::get("subscriber", ucfirst($cname) . "Controller@subscriber")->name('subscriber');
                Route::get("billing", ucfirst($cname) . "Controller@billing")->name('billing');
            }
        );
    }
);

require __DIR__.'/auth.php';