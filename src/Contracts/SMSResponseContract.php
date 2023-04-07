<?php

namespace Jundayw\SMS\Contracts;

interface SMSResponseContract
{
    public function getResponse(): mixed;

    public function isError(): bool;

    public function getMessage(): ?string;
}
