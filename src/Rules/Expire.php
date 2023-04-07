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

        if (is_string($this->getMinute())) {
            $ttl = $adapter->getTemplateParam($this->getMinute());
        }

        foreach ($adapter->getPhoneNumbers() as $phoneNumber) {
            $key = sprintf('%s.%s.%s', get_class($adapter), $adapter->getTemplateName(), $phoneNumber);
            // dump('put', $key, $value, $ttl);
            cache()->store($this->driver)->put($key, $value, $ttl * 60);
        }
    }

    public function get(SMSAdapterContract $adapter, string $recipient = null, mixed $templateName = null)
    {
        $key = sprintf('%s.%s.%s', get_class($adapter), $templateName, $recipient);
        // dump('get', $key);
        return cache()->store($this->driver)->pull($key);
    }

}