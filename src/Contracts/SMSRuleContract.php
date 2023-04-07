<?php

namespace Jundayw\SMS\Contracts;

interface SMSRuleContract
{
    public function put(SMSAdapterContract $adapter);

    public function get(SMSAdapterContract $adapter);
}
