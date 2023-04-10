<?php

use Illuminate\Support\Facades\Log;

if (!function_exists('sms')) {
    /**
     * Access SMSManager through helper.
     * @return \Jundayw\SMS\SMSManager|\Jundayw\SMS\Contracts\SMSAdapterContract
     */
    function sms()
    {
        return app(\Jundayw\SMS\Contracts\SMSManagerContract::class);
    }
}

if (!function_exists('debug_logs')) {
    function debug_logs($method)
    {
        return function ($message, array $context = []) use ($method) {
            if (config('sms.debug')) {
                Log::build([
                    'driver' => 'daily',
                    'path' => storage_path('logs/sms.log'),
                    'level' => env('LOG_LEVEL', 'debug'),
                    'days' => 14,
                ])->{$method}($message, $context);
            }
        };
    }
}