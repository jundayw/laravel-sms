<?php

namespace Jundayw\SMS\Response;

use Jundayw\SMS\Contracts\SMSResponseContract;

class Response implements SMSResponseContract
{
    private mixed $response;
    private bool $error = false;
    private ?string $message;

    public function __construct(mixed $response, callable $error, callable $message)
    {
        $this->response = $response;
        $this->error    = $error($this->response);
        $this->message  = $message($this->response);
    }

    /**
     * @return mixed
     */
    public function getResponse(): mixed
    {
        return $this->response;
    }

    /**
     * @return bool
     */
    public function isError(): bool
    {
        return $this->error;
    }

    /**
     * @return string|null
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }
}
