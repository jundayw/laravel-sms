<?php

namespace Jundayw\SMS\Hooks;

use Jundayw\SMS\Contracts\SMSAdapterContract;
use Jundayw\SMS\Contracts\SMSHookContract;

class Expire implements SMSHookContract
{
    protected mixed $field;
    protected mixed $minute;
    protected int $default;
    protected int $tries;
    protected ?string $driver;

    public function __construct(mixed $field, mixed $minute, int $default, int $tries = 3)
    {
        $this->field   = $field;
        $this->minute  = $minute;
        $this->default = $default;
        $this->tries   = $tries;
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
            debug_sms('debug')(__METHOD__, [$key, $value, $ttl * 60]);
            cache()->store($this->driver)->put($key, ['value' => $value, 'try' => 0], $ttl * 60);
        }
    }

    public function get(SMSAdapterContract $adapter, mixed $input)
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

        debug_sms('debug')(__METHOD__, [$key]);

        $cache = cache()->store($this->driver);

        if (is_null($value = $cache->get($key))) {
            return false;
        }

        if (!is_array($value)) {
            return false;
        }

        if (!array_key_exists('value', $value)) {
            return false;
        }

        if (!array_key_exists('try', $value)) {
            return false;
        }

        if ($value['try'] > $this->tries) {
            return false;
        }

        if ($value['value'] != $input) {
            $cache->put($key, ['value' => $value['value'], 'try' => $value['try'] + 1]);
            return false;
        }

        $cache->forget($key);

        return true;
    }

}