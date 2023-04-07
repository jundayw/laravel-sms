<?php

namespace Jundayw\SMS\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Jundayw\SMS\Contracts\SMSAdapterContract;
use Jundayw\SMS\Contracts\SMSResponseContract;

class SMSSent
{
    use Dispatchable, InteractsWithSockets;

    public SMSAdapterContract $adapter;
    public SMSResponseContract $response;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(SMSAdapterContract $adapter, SMSResponseContract $response)
    {
        $this->adapter  = $adapter;
        $this->response = $response;
    }
}
