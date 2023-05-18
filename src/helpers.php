<?php

use Illuminate\Support\Facades\Log;
use Jundayw\SMS\Contracts\SMSAdapterContract;
use Jundayw\SMS\Contracts\SMSManagerContract;
use Jundayw\SMS\Contracts\SMSResponseContract;
use Jundayw\SMS\SMSManager;

if (!function_exists('sms')) {
    /**
     * Access SMSManager through helper.
     *
     * @return SMSManager|SMSAdapterContract
     */
    function sms(): SMSManager|SMSAdapterContract
    {
        return app(SMSManagerContract::class);
    }
}

if (!function_exists('send_sms')) {
    /**
     * send sms
     *
     * @param mixed $to
     * @param string $templateName
     * @param array $templateParam
     * @param string|null $via
     * @param array $options
     * @return SMSResponseContract
     */
    function send_sms(mixed $to, string $templateName, array $templateParam = [], string $via = null, array $options = []): SMSResponseContract
    {
        return app(SMSManagerContract::class)
            ->via($via)
            ->to($to)
            ->scene($templateName, $templateParam)
            ->send($options);
    }
}

if (!function_exists('check_sms')) {
    /**
     * check sms
     *
     * @param mixed $to
     * @param string $templateName
     * @param mixed $input
     * @param string|null $via
     * @return bool
     */
    function check_sms(mixed $to, string $templateName, mixed $input, string $via = null): bool
    {
        return app(SMSManagerContract::class)
            ->via($via)
            ->to($to)
            ->scene($templateName)
            ->check($input);
    }
}

if (!function_exists('debug_sms')) {
    function debug_sms($method): Closure
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