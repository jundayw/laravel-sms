<?php

namespace Jundayw\SMS\Adapters;

use Illuminate\Support\Traits\Macroable;
use Jundayw\SMS\Contracts\SMSAdapterContract;
use Jundayw\SMS\Contracts\SMSResponseContract;
use Jundayw\SMS\Contracts\SMSRuleContract;
use Jundayw\SMS\Exceptions\SMSException;
use Jundayw\SMS\Response\Response;

class SMSAdapter implements SMSAdapterContract
{
    use Macroable;

    protected array $options = [];
    protected array $phoneNumbers = [];
    protected array $template = [];
    protected string $templateName;
    protected array $templateParam = [];
    protected array $templateRules = [];

    public function __construct(array $options = [])
    {
        $this->options = $options;
        $this->initialize();
    }

    protected function initialize(): void
    {
        //
    }

    /**
     * @param mixed|null $key
     * @return mixed
     */
    public function getOptions(mixed $key = null): mixed
    {
        if (is_null($key)) {
            return $this->options;
        }

        if (array_key_exists($key, $this->options)) {
            return $this->options[$key];
        }

        return null;
    }

    /**
     * @return array
     */
    public function getPhoneNumbers(): array
    {
        return $this->phoneNumbers;
    }

    /**
     * @param mixed|null $key
     * @return mixed
     */
    public function getTemplate(mixed $key = null): mixed
    {
        if (is_null($key)) {
            return $this->template;
        }

        if (array_key_exists($key, $this->template)) {
            return $this->template[$key];
        }

        return null;
    }

    /**
     * @return string|null
     */
    public function getTemplateName(): ?string
    {
        return $this->templateName;
    }

    /**
     * @param mixed|null $key
     * @return mixed
     */
    public function getTemplateParam(mixed $key = null): mixed
    {
        if (is_null($key)) {
            return $this->templateParam;
        }

        if (array_key_exists($key, $this->templateParam)) {
            return $this->templateParam[$key];
        }

        return null;
    }

    /**
     * @return array
     */
    public function getTemplateRules(): array
    {
        return $this->templateRules;
    }

    public function to(mixed $recipients): static
    {
        if (is_string($recipients)) {
            $recipients = explode(',', $recipients);
        }

        if (is_array($recipients)) {
            $recipients = array_map(function ($recipient) {
                return trim($recipient);
            }, $recipients);
            $recipients = array_filter($recipients, function ($recipient) {
                return strlen($recipient);
            });
        }

        $this->phoneNumbers = $recipients;

        return $this;
    }

    public function scene(string $templateName, array $templateParam = []): static
    {
        if (!array_key_exists($templateName, $this->options['scene'])) {
            throw new SMSException("template {$templateName} not found.");
        }

        $this->template      = $this->options['scene'][$templateName];
        $this->templateName  = $templateName;
        $this->templateParam = $templateParam;
        if (array_key_exists($rules = 'rules', $this->template)) {
            $this->templateRules = $this->template[$rules];
        }

        return $this;
    }

    public function send(mixed $options = []): SMSResponseContract
    {
        return new Response([], function ($response) {
            return true;
        }, function ($response) {
            return 'No requests sent';
        });
    }

    public function check(mixed $input): bool
    {
        $recipient = $this->getPhoneNumbers();

        if (is_string($recipient)) {
            $recipient = explode(',', $recipient);
        }

        if (count($recipient) < 1) {
            return false;
        }

        $recipient = current($recipient);

        $templateName = $this->getTemplateName();

        if (is_null($templateName)) {
            return false;
        }

        foreach ($this->getTemplateRules() as $rule) {
            if (!$rule instanceof SMSRuleContract) {
                continue;
            }
            $rule = $rule->get($this, $recipient, $templateName);
            if ($rule === true) {
                continue;
            }
            if ($rule === false || !($rule == $input)) {
                return false;
            }
        }

        return true;
    }
}
