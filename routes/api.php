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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post("/posting-va",[App\Http\Controllers\Api\SynchronizeController::class,"doPostingVA"])->name('posting_va'); // FUNCTION UPDATE DATA VA SUDAH DIBAYAR
Route::get('/sapa-api/{tgl_sts}',[App\Http\Controllers\Api\SynchronizeController::class,"doGetSynchronizeWithSapa"])->name('sapa_api'); // FUNCTION STORE DATA EREVENUE TO SAPA
Route::get('/sapa-api/sts/{no_sts}/{skpd_kode}/{sts_sapa}',[App\Http\Controllers\Api\SynchronizeController::class,"doUpdatedSynchronizeFromSapa"])->name('api_sapa_created'); // FUNCTION UPDATE DATA TERJURNAL DI SAPA
Route::get('/realisasi-pendapatan',[App\Http\Controllers\Api\SynchronizeController::class,"doSendPendapatanToEpayment"])->name('realisasi-pendapatan'); // get data realisasi sts
Route::post('/realisasi-pendapatan-synch',[App\Http\Controllers\Api\SynchronizeController::class,"doReceiveSP3BFromEpayment"])->name('realisasi-pendapatan-synch'); // get data realisasi sts
Route::get('/realisasi-api',[App\Http\Controllers\Api\SynchronizeController::class,"doRealisasiApi"])->name('realisasi-api'); // get data realisasi sts
Route::get('/synchronize-bku-akt',[App\Http\Controllers\Api\SynchronizeController::class,"doPostBatchSynchronizeBkuToAkt"])->name('synchronize-bku-akt'); // get data bkupenerimaan not success synchronize with AKT
Route::get('/get-token',[App\Http\Controllers\Api\SynchronizeController::class,"callbackKantorku"])->name('synchronize-get-token'); // get data bkupenerimaan not success synchronize with AKT
Route::get('/realisasi-pendapatan-new',[App\Http\Controllers\Api\SynchronizeController::class,"doSendPendapatanToEpaymentWithRekening"])->name('realisasi-pendapatan-new'); // get data realisasi sts
Route::post('/realisasi-pendapatan-synch-new',[App\Http\Controllers\Api\SynchronizeController::class,"doReceiveSP3BFromEpaymentWithRekening"])->name('realisasi-pendapatan-synch-new');
Route::post('/cancel-realisasi-pendapatan-synch-new',[App\Http\Controllers\Api\SynchronizeController::class,"doCancelSP3BFromEpaymentWithRekening"])->name('cancel-realisasi-pendapatan-synch-new');
Route::get('/synchronize-bku-akt-with-opd',[App\Http\Controllers\Api\SynchronizeController::class,"doPostBatchSynchronizeBkuToAkt2"])->name('synchronize-bku-akt-with-opd'); // get data bkupenerimaan not success synchronize with AKT
