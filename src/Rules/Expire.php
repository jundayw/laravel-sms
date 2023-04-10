<?php

namespace Jundayw\SMS\Rules;

use Illuminate\Support\Facades\Cache;
use Jundayw\SMS\Contracts\SMSAdapterContract;
use Jundayw\SMS\Contracts\SMSRuleContract;

class Expire implements SMSRuleContract
{
    protected mixed $field;
    protected mixed $minute;
    protected int $default;
    protected ?string $driver;

    public function __construct(mixed $field, mixed $minute, int $default)
    {
        $this->field   = $field;
        $this->minute  = $minute;
        $this->default = $default;
        $this->driver  = config('sms.cache');
    }

    /**
     * @return mixed
     */
    public function getField(): mixed
    {
        return $this->field;
    }

    /**
     * @return mixed
     */
    public function getMinute(): mixed
    {
        return $this->minute;
    }

    /**
     * @return int
     */
    public function getDefault(): int
    {
        return $this->default;
    }

    /**
     * @return string|null
     */
    public function getDriver(): ?string
    {
        return $this->driver;
    }

    public function put(SMSAdapterContract $adapter)
    {
        $value = $adapter->getTemplateParam($this->getField());
        if (is_null($value)) {
            return false;
        }

        $ttl = $this->getDefault();
        if (!is_null($this->getMinute())) {
            $ttl = $adapter->getTemplateParam($this->getMinute());
        }

        foreach ($adapter->getPhoneNumbers() as $phoneNumber) {
            $key = join('.', [get_class($adapter), $adapter->getTemplateName(), $phoneNumber]);
            debug_logs('debug')(__METHOD__, [$key, $value, $ttl * 60]);
            cache()->store($this->driver)->put($key, $value, $ttl * 60);
        }
    }

    public function get(SMSAdapterContract $adapter)
    {
        if (count($adapter->getPhoneNumbers()) < 1) {
            return false;
        }
        $recipient = current($adapter->getPhoneNumbers());

        $templateName = $adapter->getTemplateName();
        if (is_null($templateName)) {
            return false;
        }

        $key = join('.', [get_class($adapter), $templateName, $recipient]);

        debug_logs('debug')(__METHOD__, [$key]);

        return cache()->store($this->driver)->pull($key);
    }

}