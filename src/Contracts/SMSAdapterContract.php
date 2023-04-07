<?php

namespace Jundayw\SMS\Contracts;

interface SMSAdapterContract
{
    public function to(mixed $recipients): static;

    public function scene(string $templateName, array $templateParam = []): static;

    public function send(array $options = []): SMSResponseContract;

    public function check(mixed $input): bool;
}
