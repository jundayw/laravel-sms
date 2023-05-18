<?php

namespace Jundayw\SMS\Contracts;

interface SMSHookContract
{
    public function put(SMSAdapterContract $adapter);

    public function get(SMSAdapterContract $adapter, mixed $input);
}
