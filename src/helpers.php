<?php

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