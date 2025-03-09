<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\SynchronizeController;
use App\Models\Bkupenerimaan;
use App\Models\Configs;
use App\Models\Notification;
use App\Models\Stsheader;
use App\Models\Tbpheader;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        if (Session::get('lang')) {
            App::setLocale(Session::get('lang'));
        }
        if (view()->exists($request->path())) {
            return view($request->path());
        }
        return view('pages-404');
    }

    public function root()
    {
        ini_set('max_execution_time', 600); // 60 detik

        if (Session::get('lang')) {
            App::setLocale(Session::get('lang'));
        }
        $notifications = Notification::latest()->take(0)->limit(5);
        // if(auth()->user()->hasRole('administrator') || auth()->user()->hasRole('penyelia') || auth()->user()->hasRole('koor_puskesmas') || auth()->user()->hasRole('bpk') || auth()->user()->hasRole('akuntansi')){
        //     $tbpBatal = Tbpheader::where('actived',0)->count();
        //     $tbpActived = Tbpheader::where('actived',1)->count();
        //     $stsBatal = Stsheader::where('actived',0)->count();
        //     $stsActived = Stsheader::where('actived',1)->count();
        //     $bku        = Bkupenerimaan::where('actived',1)->where('bku_jenis',1)->count();
        // }else{
        //     $tbpBatal = Tbpheader::where('actived',0)->where('id_skpd',Session::get('skpd_id'))->count();
        //     $tbpActived = Tbpheader::where('actived',1)->where('id_skpd',Session::get('skpd_id'))->count();
        //     $stsBatal = Stsheader::where('actived',0)->where('id_skpd',Session::get('skpd_id'))->count();
        //     $stsActived = Stsheader::where('actived',1)->where('id_skpd',Session::get('skpd_id'))->count();
        //     $bku        = Bkupenerimaan::where('actived',1)->where('bku_jenis',1)->where('id_skpd',Session::get('skpd_id'))->count();
        //     $notifications = $notifications->where("created_by",auth()->user()->id)->latest();
        // }

        return view('index', compact('notifications'));
    }

    /*Language Translation*/
    public function lang($locale)
    {
        if ($locale) {
            App::setLocale($locale);
            Session::put('lang', $locale);
            Session::save();
            return redirect()->back()->with('locale', $locale);
        } else {
            return redirect()->back();
        }
    }
}
