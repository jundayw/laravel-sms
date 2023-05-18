<?php

namespace Jundayw\SMS\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Jundayw\SMS\Contracts\SMSHookContract;
use Jundayw\SMS\Events\SMSSent;

class SMSSentListener
{
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
     * @param SMSSent $event
     * @return void
     */
    public function handle(SMSSent $event): void
    {
        if ($event->response->isError()) {
            return;
        }
        foreach ($event->adapter->getTemplateRules() as $rule) {
            if (!$rule instanceof SMSHookContract) {
                continue;
            }
            $rule->put($event->adapter);
        }
    }
}
