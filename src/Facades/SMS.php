<?php

namespace Jundayw\SMS\Facades;

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Traits\Macroable;
use Jundayw\SMS\Contracts\SMSAdapterContract;
use Jundayw\SMS\Contracts\SMSManagerContract;
use Jundayw\SMS\Contracts\SMSResponseContract;

/**
 * @method static SMSAdapterContract via(string $via = null)
 * @method static SMSAdapterContract getDriver()
 * @method static SMSAdapterContract to(mixed $recipients)
 * @method static SMSAdapterContract scene(string $templateName, array $templateParam = [])
 * @method static SMSResponseContract send(array $options = [])
 * @method static bool check(mixed $input = null)
 *
 * @method static void macro($name, $macro)
 * @method static void mixin($mixin, $replace = true)
 * @method static bool hasMacro($name)
 * @method static void flushMacros()
 *
 * @see SMSManagerContract
 * @see SMSAdapterContract
 * @see Macroable
 */
class SMS extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return SMSManagerContract::class;
    }
}
