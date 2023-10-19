<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
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

Route::post('/login', 'AuthController@login');
Route::group([
    'middleware' => 'jwt.verify',
    // 'prefix' => 'auth/'
], function () {
    // Route::post('register', 'AuthController@register');
    // Route::post('logout', 'AuthController@logout');
    // Route::post('refresh', 'AuthController@refresh');
    // Route::post('user-profile', 'AuthController@userProfile');
});
Route::get('/api_info', function () {
    echo phpinfo();
});

// Route::get('customer','Cal\CalculationController@getCustomer'); //ex. http://localhost:6100/api/customer
// Route::get('prepareCustomer','Cal\CalculationController@prepareCustomer'); 
// Route::get('checkReceiptCancel','Cal\CalculationController@checkReceiptCancel'); 

Route::post('calInterest', 'Cal\CalculationController@getInterest'); //ex. http://localhost:6100/api/generateDate
// Route::get('calInterest','Cal\CalculationController@getInterest'); //ex. http://localhost:6100/api/generateDate
Route::post('calTDR', 'Cal\CalculationController@getTDRCalculation');
Route::post('customer', 'Cal\CustomerController@checkCustomer'); //ex. http://localhost:6100/api/customer
Route::post('Report/BasisDetail', 'Cal\ReportMonthlyController@getBasisDetailMonthly');

Route::get('calPaymentMultiTransaction', 'Cal\PaymentController@getPaymentByReceipt'); //ex. http://localhost:6100/api/calPaymentMultiTransaction
Route::get('calCollateral', 'Cal\CollateralController@getCollateralDetail');

Route::get('exportExcel', 'Cal\JobController@exportExcel');
// Route::get('UploadFile','Cal\JobController@UploadFile'); 
// Route::get('exportExcel', 'exportExcelFile')->name('export.excel');

Route::get('testDate', 'Cal\CalculationController@testDate');
Route::get('testZipFile', 'Cal\CalculationController@testZipFile');
Route::get('runExcel_SummaryBasis', 'Cal\MonthlyController@runExcel_SummaryBasis');
Route::get('runExcel_IFRS9_CF', 'Cal\MonthlyController@runExcel_IFRS9_CF');
Route::get('validateCustomerLose', 'Cal\JobController@validateCustomerLose');
