<?php

namespace Jundayw\SMS;

use Illuminate\Support\Traits\Macroable;
use Jundayw\SMS\Contracts\SMSAdapterContract;
use Jundayw\SMS\Contracts\SMSManagerContract;
use Jundayw\SMS\Exceptions\SMSException;

class SMSManager implements SMSManagerContract
{
    use Macroable {
        __call as macroCall;
    }

    public SMSAdapterContract $driver;

    public function __construct(string $via)
    {
        $this->driver = $this->via($via);
    }

    /**
     * @param string $via
     * @return SMSAdapterContract
     */
    public function via(string $via): SMSAdapterContract
    {
        $options = config("sms.{$via}");
        $driver  = config("sms.{$via}.driver");

        if (class_exists($driver)) {
            return new $driver($options);
        }

        throw new SMSException("Driver {$via} not found.");
    }

    /**
     * @return SMSAdapterContract
     */
    public function getDriver(): SMSAdapterContract
    {
        return $this->driver;
    }

    /**
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call(string $method, array $parameters): mixed
    {
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }

        return $this->driver->$method(...$parameters);
    }
}
