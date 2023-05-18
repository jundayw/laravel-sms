<?php

namespace Jundayw\SMS\Validation;

use Illuminate\Contracts\Validation\Rule;

class SMSValidation implements Rule
{
    private mixed $to;
    private string $scene;
    private ?string $via = null;

    public function __construct(mixed $to, string $scene, string $via = null)
    {
        $this->to    = $to;
        $this->scene = $scene;
        $this->via   = $via;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes(string $attribute, mixed $value): bool
    {
        if (check_sms($this->to, $this->scene, $value, $this->via)) {
            return true;
        }
        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return '验证码有误';
    }
}
