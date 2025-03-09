<?php

namespace App\Listeners;

use App\Lib\Notification;
use App\Models\User;
use App\Models\UsersLogin;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class UsersLoginSession
{
    private static $get;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        //
    }

    public function store(User $user, $skpd)
    {
        try {
            $log = new UsersLogin();
            $admin = Auth::guard('superadmin');
            if ($admin = $admin->user()) {
                /** @var Admin $admin */
                $log->admin_id = $admin->id;
            }
            $log->user_id = $user->id;
            $log->user_ip = Request::ip();;
            $log->user_skpd = $skpd->id;
            $log->location = "";
            $log->browser = Request::header('user-agent');
            $log->os = "";
            $log->longitude = "";
            $log->latitude = "";
            $log->country_code ="";
            DB::beginTransaction();
            $log->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Notification::sendException($e);
        }
    }

    /**
     * @return self
     */
    public static function get()
    {
        if (!self::$get instanceof self) {
            self::$get = new self();
        }
        return self::$get;
    }
}
