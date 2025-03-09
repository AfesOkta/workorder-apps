<?php

namespace App\Listeners;

use App\Models\User;
use App\Models\UserActivityLog;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class LogUserActivity
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

    public function store(User $user, $activity = '', $table = '', $subject = '', $desc = '')
    {
        try {
            $log = new UserActivityLog();
            $admin = Auth::guard('superadmin');
            if ($admin = $admin->user()) {
                /** @var Admin $admin */
                $log->admin_id = $admin->id;
            }
            $log->user_id = $user->id;
            $log->activity = $activity;
            $log->subject = $subject;
            $log->table = $table;
            $log->desc = $desc;
            $log->data = $user;
            $log->url = Request::fullUrl();
            $log->method = Request::method();
            $log->ip = Request::ip();
            $log->agent = Request::header('user-agent');
            DB::beginTransaction();
            $log->save();
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
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
