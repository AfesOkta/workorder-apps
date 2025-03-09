<?php

namespace App\Lib;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as BuilderQuery;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Throwable;

class Notification
{
    const DRIVER_TELEGRAM = 'telegram';
    const DRIVER_SLACK = 'slack';

    /**
     * @param $message
     */
    public static function sendMessage($message){
        switch (config('notification.driver')){
            case self::DRIVER_SLACK:
                self::sendMessageSlack($message);
                break;
            case self::DRIVER_TELEGRAM:
                self::sendMessageTelegram($message);
                break;
        }
    }


    /**
     * @param $message
     */
    public static function sendMessageTbp($message){
        switch (config('notification.driver')){
            case self::DRIVER_SLACK:
                self::sendMessageSlackTbp($message);
                break;
            case self::DRIVER_TELEGRAM:
                self::sendMessageTelegram($message);
                break;
        }
    }

    /**
     * @param $message
     */
    public static function sendMessageSts($message){
        switch (config('notification.driver')){
            case self::DRIVER_SLACK:
                self::sendMessageSlackSts($message);
                break;
            case self::DRIVER_TELEGRAM:
                self::sendMessageTelegram($message);
                break;
        }
    }

    /**
     * @param $message
     */
    public static function sendMessageBku($message){
        switch (config('notification.driver')){
            case self::DRIVER_SLACK:
                self::sendMessageSlackBku($message);
                break;
            case self::DRIVER_TELEGRAM:
                self::sendMessageTelegram($message);
                break;
        }
    }

    /**
     * @param $message
     */
    public static function sendMessageMutasi($message){
        switch (config('notification.driver')){
            case self::DRIVER_SLACK:
                self::sendMessageSlackMutasi($message);
                break;
            case self::DRIVER_TELEGRAM:
                self::sendMessageTelegram($message);
                break;
        }
    }

    /**
     * @param $message
     */
    public static function sendMessageKantorku($message){
        switch (config('notification.driver')){
            case self::DRIVER_SLACK:
                self::sendMessageSlackKantorku($message);
                break;
            case self::DRIVER_TELEGRAM:
                self::sendMessageTelegram($message);
                break;
        }
    }

    /**
     * @param Throwable $e
     */
    public static function sendException(Throwable $e){
        switch (config('notification.driver')){
            case self::DRIVER_SLACK:
                self::sendExceptionSlack($e);
                break;
            case self::DRIVER_TELEGRAM:
                self::sendExceptionTelegram($e);
                break;
        }
    }

    /**
     * @param $message
     * @return string
     */
    private static function formatMessage($message): string
    {
        $msg = $message ?: '';
        if (is_array($msg) || is_object($msg)) $msg = json_encode($msg, JSON_UNESCAPED_SLASHES);
        return substr($msg, 0, 3800);
    }

    /**
     * @param Builder|BuilderQuery $builder
     */
    public static function sendDebugDatabase($builder)
    {
        $sql_str = $builder->toSql();
        $bindings = $builder->getBindings();
        $wrapped_str = str_replace('?', "'?'", $sql_str);
        $sql = str_replace('`', '', Str::replaceArray('?', $bindings, $wrapped_str));

        if(config('notification.driver') === self::DRIVER_TELEGRAM && config('notification.telegram.parse_mode') === 'html'){
            $messages =
                "<b>SQL</b>\n" .
                "<code>" . $sql . "</code>\n" .
                "<b>Records</b>\n<code>{$builder->count()}</code> rows \n" .
                "<b>Result</b>\n" .
                "<pre>".substr(json_encode($builder->get()->toArray()), 0, 3000). "</pre>";
        }else{
            $messages =
                "*SQL*\n" .
                "`" . $sql . "`\n" .
                "*Records* \n`{$builder->count()}` rows \n" .
                "*Result* \n" .
                "```".substr(json_encode($builder->get()->toArray()), 0, 3000). "```";
        }
        self::sendMessage($messages);
    }
    //region Slack
    /**
     * @param $message
     */
    public static function sendMessageSlack($message)
    {
        $endpoint = config("notification.slack.webhook_url");
        Http::withHeaders(['Content-Type' => 'application / json'])
            ->post($endpoint, [
                'text' => self::formatMessage($message)
            ]);
    }

    /**
     * @param $message
     */
    public static function sendMessageSlackTbp($message)
    {
        $endpoint = config("notification.slack_tbp.webhook_url");
        Http::withHeaders(['Content-Type' => 'application / json'])
            ->post($endpoint, [
                'text' => self::formatMessage($message)
            ]);
    }

    /**
     * @param $message
     */
    public static function sendMessageSlackSts($message)
    {
        $endpoint = config("notification.slack_sts.webhook_url");
        Http::withHeaders(['Content-Type' => 'application / json'])
            ->post($endpoint, [
                'text' => self::formatMessage($message)
            ]);
    }

    /**
     * @param $message
     */
    public static function sendMessageSlackBku($message)
    {
        $endpoint = config("notification.slack_bku.webhook_url");
        Http::withHeaders(['Content-Type' => 'application / json'])
            ->post($endpoint, [
                'text' => self::formatMessage($message)
            ]);
    }

    /**
     * @param $message
     */
    public static function sendMessageSlackMutasi($message)
    {
        $endpoint = config("notification.slack_mutasi.webhook_url");
        Http::withHeaders(['Content-Type' => 'application / json'])
            ->post($endpoint, [
                'text' => self::formatMessage($message)
            ]);
    }

    /**
     * @param $message
     */
    public static function sendMessageSlackKantorku($message)
    {
        $endpoint = config("notification.slack_kantorku.webhook_url");
        Http::withHeaders(['Content-Type' => 'application / json'])
            ->post($endpoint, [
                'text' => self::formatMessage($message)
            ]);
    }

    /**
     * @param Throwable $e
     */
    public static function sendExceptionSlack(Throwable $e)
    {
        $endpoint = config("notification.slack.webhook_url");
        Http::withHeaders(['Content-Type' => 'application / json'])
            ->post($endpoint, [
                'text' => self::formatMessageExceptionSlack($e)
            ]);
    }

    /**
     * @param Throwable $e
     * @return string
     */
    private static function formatMessageExceptionSlack(Throwable $e): string
    {
        $routeConv = '';
        $routeMsg = Route::current();
        $params = Route::getCurrentRequest() ? json_encode(Route::getCurrentRequest()->toArray()) : '';
        if ($routeMsg) {
            $middleware = json_encode($routeMsg->computedMiddleware);
            $getController = stripslashes(json_encode($routeMsg->getAction('controller')));
            $routeConv = "`Url : " . url('') . '/' . $routeMsg->uri . "`\n" .
                "`Request : " . $params . "`\n" .
                "`Middleware : " . $middleware . "`\n" .
                "`Controller : " . $getController . "`\n";
        }
        $controller = $routeConv ? "> *Route*\n$routeConv\n" : '';
        $class = get_class($e);
        $code = $e->getCode();
        $message = $e->getMessage() ? "> *Error Message* \n`{$e->getMessage()}`\n" : '';
        $codeMsg = $code ? " *Code* `$code` " : '';
        $trace = $e->getTraceAsString();

        $base = base_path();
        $trace = str_replace($base, 'Server', $trace);
        $file = str_replace($base, 'Server', $e->getFile());

        $vendorIluminate = 'vendor/laravel/framework/src/Illuminate/';
        $vendorIluminate_ = 'vendor\laravel\framework\src\Illuminate\\';
        $trace = substr(str_replace($vendorIluminate, 'Laravel/', $trace), 0, 2800);
        $trace = substr(str_replace($vendorIluminate_, 'Laravel\\', $trace), 0, 2800);
        $file = str_replace($vendorIluminate, 'Laravel/', $file);
        $file = str_replace($vendorIluminate_, 'Laravel\\', $file);
        $time = date('d-m-Y H:i:s');
        $ip = isset($_SERVER["REMOTE_ADDR"]) ?  "> *IP*\n ```{$_SERVER["REMOTE_ADDR"]}```\n" : '';
        $session = Auth::guard('admin')->user()?:Auth::user();
        $user = '';
        if($session && !empty($session->toJson)) {
                $user = "`$session->toJson()`\n";
        }
        $app_env = strtoupper(config('app.env'));
        $msg = ":fire: *ERROR $app_env*$codeMsg:fire:\n" .
            "> *URL*\n `" . url()->full() . "`\n" .
            $controller .
            $user .
            "> *Time*\n `$time`\n" .
            $ip .
            $message .
            "> *Class*\n `$class`\n" .
            "> *Path*\n `Server = $base` \n `Laravel = $vendorIluminate`\n" .
            "> *File*\n `$file - Line : {$e->getLine()}`\n" .
            "> *Trace*\n ```$trace```\n" .
            "`____________________END_OF_ERROR____________________`";
        return self::formatMessage($msg);
    }
    //endregion

    //region Telegram
    /**
     * @param $message
     * @return Response
     */
    public static function sendMessageTelegram($message): Response
    {
        $telegram_token = config('notification.telegram.bot_token');
        $chat_id = config('notification.telegram.group_chat_id');
        $parse_mode = config('notification.telegram.parse_mode');
        $endpoint = "https://api.telegram.org/bot$telegram_token/sendMessage";

        return Http::withHeaders(['Content-Type' => 'application / json'])
            ->get($endpoint, [
                'parse_mode' => $parse_mode,
                'chat_id' => $chat_id,
                'text' => $message
            ]);
    }

    /**
     * @param $e
     * @return Response
     */
    public static function sendExceptionTelegram($e): Response
    {
        $telegram_token = config('notification.telegram.bot_token');
        $chat_id = config('notification.telegram.group_chat_id');
        $parse_mode = config('notification.telegram.parse_mode');
        $endpoint = "https://api.telegram.org/bot$telegram_token/sendMessage";

        return Http::withHeaders(['Content-Type' => 'application / json'])
            ->get($endpoint, [
                'parse_mode' => $parse_mode,
                'chat_id' => $chat_id,
                'text' =>  self::formatMessageExceptionTelegram($e)
            ]);
    }

    /**
     * @param $e
     * @return string
     */
    private static function formatMessageExceptionTelegram($e): string
    {
        $routeConv = '';
        $routeMsg = Route::current();
        $params = Route::getCurrentRequest() ? json_encode(Route::getCurrentRequest()->toArray()) : '';
        if ($routeMsg) {
            $middleware = json_encode($routeMsg->computedMiddleware);
            $getController = stripslashes(json_encode($routeMsg->getAction('controller')));
            $routeConv = "<code>Url : " . url('') . '/' . $routeMsg->uri . "</code>\n" .
                "<code>Request : " . $params . "</code>\n" .
                "<code>Middleware : " . $middleware . "</code>\n" .
                "<code>Controller : " . $getController . "</code>\n";
        }
        $controller = $routeConv ? "<b>Route</b>\n$routeConv\n" : '';
        $class = get_class($e);
        $code = $e->getCode();
        $message = $e->getMessage() ? "<b>Error Message<b> \n<code>{$e->getMessage()}</code>\n" : '';
        $codeMsg = $code ? "<b>Code</b> <code>$code</code> " : '';
        $base = base_path();
        $trace = str_replace($base, 'Server', $e->getTraceAsString());

        $vendorIluminate = 'vendor/laravel/framework/src/Illuminate/';
        $vendorIluminate_ = 'vendor\laravel\framework\src\Illuminate\\';
        $trace = substr(str_replace($vendorIluminate, 'Laravel/', $trace), 0, 2800);
        $trace = substr(str_replace($vendorIluminate_, 'Laravel\\', $trace), 0, 2800);
        $file = str_replace($vendorIluminate, 'Laravel/', str_replace($base, 'Server', $e->getFile()));
        $file = str_replace($vendorIluminate_, 'Laravel\\', $file);
        $time = date('d-m-Y H:i:s');
        $ip = isset($_SERVER["REMOTE_ADDR"]) ?  "<b>IP</b>\n <code>{$_SERVER["REMOTE_ADDR"]}</code>\n" : '';
        $session = Auth::guard('admin')->user()?:Auth::user();
        $user = '';
        if($session){
            $user = "<code>".json_encode($session->toJson())."</code>\n";
        }
        $app_env = strtoupper(config('app.env'));
        $msg = "<b>ERROR $app_env</b>$codeMsg\n" .
            "<b>URL</b>\n <code>" . url()->full() . "</code>\n" .
            $controller .
            $user .
            "<b>Time</b>\n <code>$time</code>\n" .
            $ip.
            $message .
            "<b>Class</b>\n<code>$class</code>\n" .
            "<b>Path</b>\n <code>Server = $base</code> \n <code>Laravel = $vendorIluminate</code>\n" .
            "<b>File</b>\n <code>$file - Line : {$e->getLine()}</code>\n" .
            "<b>Trace</b>\n <code>$trace</code>\n" .
            "<code>____________________END_OF_ERROR____________________</code>";
        return self::formatMessage($msg);
    }
    //endregion
}
