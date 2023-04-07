<?php

namespace Jundayw\SMS\Contracts;

interface SMSManagerContract
{
    public function via(string $via): SMSAdapterContract;
}
