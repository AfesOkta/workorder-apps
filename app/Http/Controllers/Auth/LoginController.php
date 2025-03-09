<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Lib\Notification;
use App\Listeners\LogUserActivity;
use App\Listeners\UsersLoginSession;
use App\Models\SkpdBendahara;
use App\Models\User;
use App\Models\UsersLogin;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    protected $username;
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->username = $this->findUsername();
    }

    public function login(Request $request)
    {
        try {
            $this->validateLogin($request);

            // If the class is using the ThrottlesLogins trait, we can automatically throttle
            // the login attempts for this application. We'll key this by the username and
            // the IP address of the client making these requests into this application.
            if ($this->hasTooManyLoginAttempts($request)) {
                $this->fireLockoutEvent($request);

                return $this->sendLockoutResponse($request);
            }
            $userExist = User::whereUsername($request->{$this->username()})->first();
            if($request->{$this->username()} == "admin1") {
                $userExist = User::whereUsername($request->{$this->username()})->first();
            }
            if ($userExist) {
                if($userExist->hasRole('administrator') || $userExist->hasRole('penyelia') || $userExist->hasRole('bpk') || $userExist->hasRole('akuntansi')){
                    $this->attemptLogin($request);
                    LogUserActivity::get()->store($userExist, 'Login Attempt', User::class, "Login Attempt");
                    Notification::sendMessage("Username ".$request->{$this->username()}." Successfully login apps");
                    return redirect()->route('dashboard');
                }
                $countSkpd = SkpdBendahara::where('id_bendahara', $userExist->id)->where('actived', 1)->count();
                if ($countSkpd > 1) {
                    return redirect(route('opsi_session',['username'=>$userExist->username,'password'=>$request->password,'email'=>$userExist->email]));
                }else{
                    $skpd_id = SkpdBendahara::where('id_bendahara', $userExist->id)->where('actived', 1)->firstOrFail();
                    Session::put("skpd_id", $skpd_id->id_skpd);
                    Session::put("bendahara_id", $skpd_id->id_bendahara);
                    LogUserActivity::get()->store($userExist, 'Login Attempt', User::class, "Login Attempt");
                    UsersLoginSession::get()->store($userExist,$skpd_id);
                    Notification::sendMessage("Username ".$userExist->name." with NIP ".$request->{$this->username()}." Successfully login apps");
                }
                $this->attemptLogin($request);
                // Customize the response based on your requirements
                // if ($user->isAdmin()) {
                //     return redirect()->route('admin.dashboard');
                // } elseif ($user->isModerator()) {
                //     return redirect()->route('moderator.dashboard');
                // } else {
                // return $this->sendLoginResponse($request);
                return redirect()->route('dashboard');
            }


            // If the login attempt was unsuccessful we will increment the number of attempts
            // to login and redirect the user back to the login form. Of course, when this
            // user surpasses their maximum number of attempts they will get locked out.
            $this->incrementLoginAttempts($request);

            return $this->sendFailedLoginResponse($request);
        } catch (\Throwable $th) {
            return $this->sendFailedLoginResponse($request);
        }

    }

    public function findUsername()
    {
        $login = request()->input('username');

        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        request()->merge([$fieldType => $login]);
        return $fieldType;
    }

    public function username()
    {
        return $this->username;
    }

    protected function validateLogin(Request $request)
    {
        $validation_rule = [
            $this->username() => 'required|string',
            'password' => 'required|string',
        ];

        $request->validate($validation_rule);
    }

    public function logout()
    {
        $userId = auth()->id();
        $user = User::whereId($userId)->first();
        //region Send User Activity Log
        LogUserActivity::get()->store($user, 'Logout', UserLogin::class, null, $user->username . " Logged Out");
        //endregion
        Cache::forget("is_online_web_" . $userId);
        // clearAllCache();
        $this->guard()->logout();

        // Remove flush session
        // request()->session()->invalidate();
        Session::flush();
        $notify[] = ['success', __("You have been logged out.")];
        return redirect()->route('user.login')->withNotify($notify);
    }
}
