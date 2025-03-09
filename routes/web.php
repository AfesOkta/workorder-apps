<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\UserSessionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
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

Auth::routes();
Route::get('/sso/auth', [App\Http\Controllers\Api\SynchronizeController::class, 'getAutorizeToken'])->name('sso.auth');
Route::get('/sso/callback', [App\Http\Controllers\Api\SynchronizeController::class, 'callbackKantorku'])->name('sso.callback');
Route::get('/', [App\Http\Controllers\HomeController::class, 'root'])->name('dashboard');
Route::get('opsi_session', [UserSessionController::class, 'doPilihSKPD'])->name('opsi_session');
Route::post('opsi_session', [UserSessionController::class, 'doSelectSKPD'])->name('pilih_opsi_session');
Route::get('{any}', [App\Http\Controllers\HomeController::class, 'index']);

//Language Translation
Route::get('index/{locale}', [App\Http\Controllers\HomeController::class, 'lang']);

Route::group(['middleware' => ['auth']], function() {

    Route::resource('roles', RoleController::class);
    // Route::resource('users', UserController::class);
    // Route::resource('products', ProductController::class);
});

Route::name('user.')->group(function () {
    Route::get('/login', [LoginController::class,'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class,'login']);
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    //Route::resource('users', UserController::class);
});

Route::group([
    'prefix' => 'settings',
    'middleware' => 'auth',
    ],function() {
        Route::group([
            'prefix' => 'roles'
        ], function(){
            Route::get('/', [App\Http\Controllers\RoleController::class, 'index'])->name('roles');
            Route::get('/show/{id}',[App\Http\Controllers\RoleController::class, 'show'])->name('settings.roles.show');
            Route::post('/store', [App\Http\Controllers\RoleController::class, 'store'])->name('settings.roles.store');
            Route::post('/update', [App\Http\Controllers\RoleController::class, 'update'])->name('settings.roles.update');
            Route::post('/delete', [App\Http\Controllers\RoleController::class, 'delete'])->name('settings.roles.delete');
        });
        Route::group([
            'prefix' => 'users'
        ], function(){
            Route::get('/', [App\Http\Controllers\UserController::class, 'index'])->name('settings.users');
            Route::get('/create', [App\Http\Controllers\UserController::class, 'create_skpd'])->name('settings.users.create');
            Route::get('/json', [App\Http\Controllers\UserController::class, 'json'])->name('settings.users.json');
            Route::get('/{id}/edit',[App\Http\Controllers\UserController::class, 'edit'])->name('settings.kota.show');
            Route::post('/store', [App\Http\Controllers\UserController::class, 'store'])->name('settings.users.store');
            // Route::post('/update', [App\Http\Controllers\Master\KotaController::class, 'update'])->name('settings.kota.update');
            Route::post('/delete', [App\Http\Controllers\UserController::class, 'destroy'])->name('settings.users.delete');
            // Route::post('/upload', [App\Http\Controllers\Master\KotaController::class, 'upload'])->name('settings.kota.upload');
            Route::get('/lock-screen',[App\Http\Controllers\UserController::class,'showLockScreenForm'])->name('settings.users.lock-screen');
            Route::get('/view-profile',[App\Http\Controllers\UserController::class,'viewProfile'])->name('settings.users.profile');
            Route::post('/update-password',[App\Http\Controllers\UserController::class,'updatePassword'])->name('settings.users.update.password');
            Route::get('/direct-login',[App\Http\Controllers\UserController::class,'directLogin'])->name('settings.users.directlogin');
        });
        Route::group([
            'prefix' => 'permission'
        ], function(){
            Route::get('/', [App\Http\Controllers\RoleController::class, 'index'])->name('roles');
            // Route::get('/show/{id}',[App\Http\Controllers\Master\KotaController::class, 'show'])->name('settings.kota.show');
            // Route::post('/store', [App\Http\Controllers\Master\KotaController::class, 'store'])->name('settings.kota.store');
            // Route::post('/update', [App\Http\Controllers\Master\KotaController::class, 'update'])->name('settings.kota.update');
            // Route::post('/delete', [App\Http\Controllers\Master\KotaController::class, 'delete'])->name('settings.kota.delete');
            // Route::post('/upload', [App\Http\Controllers\Master\KotaController::class, 'upload'])->name('settings.kota.upload');
        });

        Route::group([
            'prefix' => 'configs'
        ], function(){
            Route::get('/', [App\Http\Controllers\Master\MasterController::class, 'index_config'])->name('configs');
            // Route::get('/show/{id}',[App\Http\Controllers\Master\KotaController::class, 'show'])->name('settings.kota.show');
            Route::post('/store', [App\Http\Controllers\Master\MasterController::class, 'store_config'])->name('settings.configs.store');
            // Route::post('/update', [App\Http\Controllers\Master\KotaController::class, 'update'])->name('settings.kota.update');
            // Route::post('/delete', [App\Http\Controllers\Master\KotaController::class, 'delete'])->name('settings.kota.delete');
            // Route::post('/upload', [App\Http\Controllers\Master\KotaController::class, 'upload'])->name('settings.kota.upload');
        });

        Route::group([
            'prefix' => 'notification'
        ], function(){
            Route::get('/', [App\Http\Controllers\Master\MasterController::class, 'index_notif'])->name('settings.notification');
            Route::get('/search', [App\Http\Controllers\Master\MasterController::class, 'searchNotif'])->name('settings.notification.json');
        });
});

Route::group([
    'prefix' => 'master',
    'middleware' => 'auth',
    ],function() {
        Route::group([
            'prefix' => 'skpd'
        ], function(){
            Route::get('/', [App\Http\Controllers\Master\MasterController::class, 'index_skpd'])->name('master.skpd');
            Route::get('/list', [App\Http\Controllers\Master\MasterController::class, 'doGetSkpdList'])->name('master.skpd.get.list');
            Route::get('/show/{id}',[App\Http\Controllers\RoleController::class, 'show_skpd'])->name('master.skpd.show');
            Route::post('/delete', [App\Http\Controllers\Master\MasterController::class, 'delete_skpd'])->name('master.skpd.delete');
            Route::post('/synchronize-skpd', [App\Http\Controllers\Master\MasterController::class, 'sync_skpd'])->name('master.skpd.synchronize');
            Route::get('/create-bendahara', [App\Http\Controllers\Master\MasterController::class, 'add_bendahara'])->name('master.skpd.create.bendahara');
            Route::get('/edit-bendahara', [App\Http\Controllers\Master\MasterController::class, 'edit_bendahara'])->name('master.skpd.edit.bendahara');
            Route::post('/store-bendahara', [App\Http\Controllers\Master\MasterController::class, 'store_bendahara'])->name('master.skpd.store.bendahara');
            Route::post('/delete-bendahara', [App\Http\Controllers\Master\MasterController::class, 'delete_bendahara'])->name('master.skpd.delete.bendahara');
            Route::get('/create-anggaran', [App\Http\Controllers\Master\MasterController::class, 'add_anggaran'])->name('master.skpd.create.anggaran.rekening');
            Route::post('/sync-anggaran', [App\Http\Controllers\Master\MasterController::class, 'sync_anggaran'])->name('master.skpd.sync.anggaran.rekening');
            Route::post('/process_all_synchronize',[App\Http\Controllers\Master\MasterController::class,'process_all_synchronize'])->name('master.skpd.sync.process.all.anggaran.rekening');
            Route::post('/process_synchronize',[App\Http\Controllers\Master\MasterController::class,'process_synchronize'])->name('master.skpd.sync.process.anggaran.rekening');
            Route::get('/select-skpd',[App\Http\Controllers\Master\MasterController::class,'selectSkpd'])->name('master.skpd.select');
            Route::get('/create-otorisator', [App\Http\Controllers\Master\MasterController::class, 'add_otorisator'])->name('master.skpd.create.otorisator');
            Route::get('/edit-otorisator', [App\Http\Controllers\Master\MasterController::class, 'edit_otorisator'])->name('master.skpd.edit.otorisator');
            Route::post('/store-otorisator', [App\Http\Controllers\Master\MasterController::class, 'store_otorisator'])->name('master.skpd.store.otorisator');
            Route::post('/delete-otorisator', [App\Http\Controllers\Master\MasterController::class, 'delete_otorisator'])->name('master.skpd.delete.otorisator');
            Route::get('/select-skpd-penghasil',[App\Http\Controllers\Master\MasterController::class,'selectSkpdPenghasil'])->name('master.skpd.select.penghasil');
            Route::get('/select-skpd-bendahara/{skpd}/select',[App\Http\Controllers\Master\MasterController::class,'selectBendaharaBySkpd'])->name('master.skpd.select.bendahara');
            Route::get('/select-skpd-otorisator/{skpd}/select',[App\Http\Controllers\Master\MasterController::class,'selectOtorisatorBySkpd'])->name('master.skpd.select.otorisator');
            Route::post('/store-skpd', [App\Http\Controllers\Master\MasterController::class, 'store_skpd'])->name('master.skpd.store');
        });
        Route::group([
            'prefix' => 'rekening'
        ], function(){
            Route::get('/', [App\Http\Controllers\Master\MasterController::class, 'index_rekening'])->name('master.rekening');
            Route::get('/list-rekening', [App\Http\Controllers\Master\MasterController::class, 'listRekening'])->name('master.rekening.list');
            Route::post('/synchronize-rekening', [App\Http\Controllers\Master\MasterController::class, 'sync_rekening'])->name('master.rekening.synchronize');
            Route::get('/{idkegiatan}/{idskpd}/select',[App\Http\Controllers\Master\MasterController::class, 'selectRekeningByKegiatanSkpd'])->name('master.rekening.kegiatan.skpd.select');
            // Route::post('/store', [App\Http\Controllers\Master\KotaController::class, 'store'])->name('settings.kota.store');
            // Route::post('/update', [App\Http\Controllers\Master\KotaController::class, 'update'])->name('settings.kota.update');
            // Route::post('/delete', [App\Http\Controllers\Master\KotaController::class, 'delete'])->name('settings.kota.delete');
            // Route::post('/upload', [App\Http\Controllers\Master\KotaController::class, 'upload'])->name('settings.kota.upload');
        });
        Route::group([
            'prefix' => 'kegiatan'
        ], function(){
            Route::get('/', [App\Http\Controllers\Master\MasterController::class, 'index_kegiatan'])->name('master.kegiatan');
            Route::post('/synchronize-kegiatan', [App\Http\Controllers\Master\MasterController::class, 'sync_kegiatan'])->name('master.kegiatan.synchronize');
            Route::get('/list-kegiatan', [App\Http\Controllers\Master\MasterController::class, 'listKegiatan'])->name('master.kegiatan.list');
            Route::post('/delete', [App\Http\Controllers\Master\MasterController::class, 'delete'])->name('master.kegiatan.delete');
            Route::get('/select', [App\Http\Controllers\Master\KegiatanController::class, 'selectKegiatan'])->name('master.kegiatan.select');
            Route::get('/select-bku', [App\Http\Controllers\Master\KegiatanController::class, 'selectKegiatanInBku'])->name('master.kegiatan.select.in.bku');
            // Route::post('/upload', [App\Http\Controllers\Master\KotaController::class, 'upload'])->name('settings.kota.upload');
        });

        Route::group([
            'prefix' => 'tahapan'
        ], function(){
            Route::get('/', [App\Http\Controllers\Master\MasterController::class, 'index_tahapan'])->name('master.tahapan');
            Route::post('/synchronize-tahapan', [App\Http\Controllers\Master\MasterController::class, 'sync_tahapan'])->name('master.tahapan.synchronize');
            Route::get('/list-tahapan', [App\Http\Controllers\Master\MasterController::class, 'listTahapan'])->name('master.tahapan.list');
            // Route::post('/store', [App\Http\Controllers\Master\KotaController::class, 'store'])->name('settings.kota.store');
            // Route::post('/update', [App\Http\Controllers\Master\KotaController::class, 'update'])->name('settings.kota.update');
            Route::post('/delete', [App\Http\Controllers\Master\MasterController::class, 'deleteTahapan'])->name('master.tahapan.delete');
            // Route::post('/upload', [App\Http\Controllers\Master\KotaController::class, 'upload'])->name('settings.kota.upload');
        });

        Route::group([
            'prefix' => 'anggaran'
        ], function(){
            Route::get('/', [App\Http\Controllers\Master\MasterController::class, 'index_anggaran'])->name('master.anggaran');
            Route::get('/search', [App\Http\Controllers\Master\MasterController::class, 'getAnggaranBySKPDKegiatanRekeningTahapan'])->name('master.anggaran.search');
            Route::get('/{idkegiatan}/{idskpd}/select',[App\Http\Controllers\Master\MasterController::class, 'getSelectRekeningByKegiatanSkpd'])->name('master.rekening.kegiatan.skpd.select');
            Route::get('/{idkegiatan}/{idskpd}/{tahapan}/select',[App\Http\Controllers\Master\MasterController::class, 'getSelectRekeningByKegiatanSkpdTahapan'])->name('master.rekening.kegiatan.skpd.tahapan.select');
            Route::post('/synchronize-tahapan', [App\Http\Controllers\Master\MasterController::class, 'sync_anggaran'])->name('master.anggaran.synchronize');
            Route::post('/store', [App\Http\Controllers\Master\MasterController::class, 'store_anggaran'])->name('master.anggaran.store.manual');
            Route::post('/update', [App\Http\Controllers\Master\MasterController::class, 'update_anggaran'])->name('master.anggaran.update');
            Route::post('/delete', [App\Http\Controllers\Master\MasterController::class, 'delete'])->name('master.anggaran.delete');
            Route::get('/rekening/select',[App\Http\Controllers\Master\RekeningController::class, 'selectRekening'])->name('master.rekening.select');
        });
});


Route::group([
    'prefix' => 'transaction',
    'middleware' => 'auth',
    ],function() {
        Route::group([
            'prefix' => 'work-order'
        ], function(){
            Route::get('/', [App\Http\Controllers\Transaksi\WorkOrdersController::class, 'index'])->name('transaksi.wo.index');
            Route::get('/search', [App\Http\Controllers\Transaksi\WorkOrdersController::class, 'search'])->name('transaksi.wo.search');
            Route::get('/tambah', [App\Http\Controllers\Transaksi\WorkOrdersController::class, 'create'])->name('transaksi.wo.tambah');
            Route::post('/save', [App\Http\Controllers\Transaksi\WorkOrdersController::class, 'store'])->name('transaksi.wo.save');
            Route::get('/edit/{id}', [App\Http\Controllers\Transaksi\WorkOrdersController::class, 'show'])->name('transaksi.wo.edit');
            Route::get('/rincian/search', [App\Http\Controllers\Transaksi\WorkOrdersController::class, 'dataTableRincian'])->name('transaksi.wo.rincian');
            Route::get('show/rincian/{id}', [App\Http\Controllers\Transaksi\WorkOrdersController::class, 'showRincian'])->name('transaksi.wo.rincian.show');
            Route::get('download/rincian', [App\Http\Controllers\Transaksi\WorkOrdersController::class, 'downloadRincian'])->name('transaksi.wo.rincian.download');
            Route::get('download/wo', [App\Http\Controllers\Transaksi\WorkOrdersController::class, 'downloadwo'])->name('transaksi.wo.download');
            Route::post('/rincian/save', [App\Http\Controllers\Transaksi\WorkOrdersController::class, 'storeRincian'])->name('transaksi.wo.rincian.save');
            Route::post('/rincian/delete', [App\Http\Controllers\Transaksi\WorkOrdersController::class, 'deleteRincian'])->name('transaksi.wo.delete.rincian');
            Route::post('import/rincian', [App\Http\Controllers\Transaksi\WorkOrdersController::class, 'importRincian'])->name('transaksi.wo.rincian.import');
            Route::post('import', [App\Http\Controllers\Transaksi\WorkOrdersController::class, 'importwo'])->name('transaksi.wo.import');
            Route::get('/report/wo', [App\Http\Controllers\Report\ReportController::class,'reportwo'])->name('transaksi.report.wo');
            Route::post('sync-sts', [App\Http\Controllers\Transaksi\WorkOrdersController::class, 'synchronizeSts'])->name('transaksi.wo.sync.sts');
            Route::get('sync-sts-json', [App\Http\Controllers\Transaksi\WorkOrdersController::class, 'synchronizeJsonSts'])->name('transaksi.wo.sync.json.sts');
            Route::get('sync-sts-json-detail', [App\Http\Controllers\Transaksi\WorkOrdersController::class, 'synchronizeDetailJsonSts'])->name('transaksi.wo.sync.json.sts.detail');
            Route::post('sync-sts/process', [App\Http\Controllers\Transaksi\WorkOrdersController::class, 'processwoToSts'])->name('transaksi.wo.process.sts');
            Route::post('delete', [App\Http\Controllers\Transaksi\WorkOrdersController::class, 'delete'])->name('transaksi.wo.delete');
            Route::post('showLogs/{id}', [App\Http\Controllers\Transaksi\WorkOrdersController::class, 'showLogs'])->name('transaksi.wo.logs');
        });
});

Route::group([
    'prefix' => 'kontrol-data',
    'middleware' => 'auth',
],function() {
    Route::group([
        'prefix' => 'register-sts'
    ], function(){
        Route::get('/', [App\Http\Controllers\Report\ReportController::class, 'indexRegister'])->name('kontrol-data.register.index');
        Route::get('search', [App\Http\Controllers\Report\ReportController::class, 'searchRegister'])->name('kontrol-data.register.search');
        Route::post('cetak', [App\Http\Controllers\Report\ReportController::class, 'cetakRegister'])->name('kontrol-data.register.cetak');
    });
    Route::group([
        'prefix' => 'realisasi-sts'
    ], function(){
        Route::get('/', [App\Http\Controllers\Report\ReportController::class, 'indexRealisasi'])->name('kontrol-data.realisasi.index');
        Route::get('search', [App\Http\Controllers\Report\ReportController::class, 'searchRealisasi'])->name('kontrol-data.realisasi.search');
        Route::post('cetak', [App\Http\Controllers\Report\ReportController::class, 'cetakRealisasi'])->name('kontrol-data.realisasi.cetak');
    });

    Route::group([
        'prefix' => 'bku'
    ], function(){
        Route::get('/show', [App\Http\Controllers\Report\ReportController::class, 'indexBku'])->name('kontrol-data.bku.index');
        Route::get('/show-tunai', [App\Http\Controllers\Report\ReportController::class, 'indexBku'])->name('kontrol-data.bku.tunai');
        Route::get('/show-transfer', [App\Http\Controllers\Report\ReportController::class, 'indexBku'])->name('kontrol-data.bku.transfer');
        Route::post('cetak', [App\Http\Controllers\Report\ReportController::class, 'cetakBku'])->name('kontrol-data.bku.cetak');
        Route::post('cetak-tunai', [App\Http\Controllers\Report\ReportController::class, 'cetakBkuTunai'])->name('kontrol-data.bku.cetak-tunai');
        Route::post('cetak-bank', [App\Http\Controllers\Report\ReportController::class, 'cetakBkuBank'])->name('kontrol-data.bku.cetak-bank');
        Route::get('export-cetak-bank', [App\Http\Controllers\Report\ReportController::class, 'exportExcelBkuBank'])->name('kontrol-data.bku.export-cetak-bank');
    });

    Route::group([
        'prefix' => 'fungsional'
    ], function(){
        Route::get('/', [App\Http\Controllers\Report\ReportController::class, 'indexFungsional'])->name('kontrol-data.fungsional.index');
        Route::post('cetak', [App\Http\Controllers\Report\ReportController::class, 'cetakFungsional'])->name('kontrol-data.fungsional.cetak');
    });
});



Route::get('/siap/print/sts/{id}',[App\Http\Controllers\Report\ReportController::class,'view_cetak_sts'])->name('view_cetak_sts');
Route::get('/siap/print/wo/{id}',[App\Http\Controllers\Report\ReportController::class,'view_cetak_wo'])->name('view_cetak_wo');
