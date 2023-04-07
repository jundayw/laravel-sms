<?php

namespace Jundayw\SMS\Contracts;

interface SMSAdapterContract
{
    public function to(mixed $recipients): static;

    public function code(string $templateName, array $templateParam = []): static;

    public function send(array $options = []): SMSResponseContract;

    public function check(mixed $input): bool;
}
