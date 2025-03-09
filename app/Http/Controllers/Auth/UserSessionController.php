<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Lib\Notification;
use App\Listeners\LogUserActivity;
use App\Listeners\UsersLoginSession;
use App\Models\User;
use App\Repositories\Impl\SKPDBendaharaImplement;
use App\Repositories\Impl\SKPDImplement;
use App\Repositories\Impl\UserImplement;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class UserSessionController extends Controller
{
    protected $username;
    use AuthenticatesUsers;

    public $skpdBendaharaRepo;
    public $skpdRepo;
    public $userRepo;
    public function __construct(SKPDBendaharaImplement $skpdBendaharaRepo, SKPDImplement $skpdRepo, UserImplement $userRepo) {
        $this->skpdBendaharaRepo = $skpdBendaharaRepo;
        $this->skpdRepo = $skpdRepo;
        $this->userRepo = $userRepo;
    }

    public function doPilihSKPD(Request $request)
    {
        $userId = User::whereUsername($request->username)->first()->id;
        $skpd = $this->skpdBendaharaRepo->getFilterByUser($userId);
        $password = $request->password;
        $username = $request->username;
        $email = $request->email;
        return view('session.auth-choose-skpd', compact('skpd','userId','password','username','email'));
    }

    public function doSelectSKPD(Request $request)
    {
        $this->username = $request->username;
        $this->validateLogin($request);

        $skpd = $this->skpdRepo->find($request->skpdid);
        $user = $this->userRepo->find($request->userid);

        Session::put("skpd_id", $skpd->id);
        Session::put("bendahara_id", $user->id_skpd);

        LogUserActivity::get()->store($user, 'Login Attempt', User::class, "Login Attempt");
        UsersLoginSession::get()->store($user,$skpd->id);
        Notification::sendMessage("Username ".$user->username." Successfully login apps");

        $this->attemptLogin($request);

        return redirect()->route('dashboard');
    }

    protected function validateLogin(Request $request)
    {
        $validation_rule = [
            $this->username() => 'required|string',
            'password' => 'required|string',
        ];

        $request->validate($validation_rule);
    }

}
