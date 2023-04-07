<?php

namespace Jundayw\SMS\Adapters;

use Jundayw\SMS\Contracts\SMSResponseContract;
use Jundayw\SMS\Events\SMSSent;
use Jundayw\SMS\Response\Response;

class QCloudSMSAdapter extends SMSAdapter
{
    public function scene(string $templateName, array $templateParam = []): static
    {
        parent::scene($templateName, $templateParam);
        $i      = 1;
        $params = [];
        foreach ($this->templateParam as $param) {
            $params[$i++] = $param;
        }
        $this->templateParam = $params;
        return $this;
    }

    public function send(mixed $options = []): SMSResponseContract
    {
        $response = '{
                "Response": {
                "Error": {
                    "Code": "AuthFailure.SignatureFailure",
                    "Message": "The provided credentials could not be validated. Please check your signature is correct."
                },
                "RequestId": "ed93f3cb-f35e-473f-b9f3-0d451b8b79c6"
            }
        }';
        $response = '    {
            "Response": {
                "TotalCount": 0,
                "InstanceStatusSet": [],
                "RequestId": "b5b41468-520d-4192-b42f-595cc34b6c1c"
            }
        }';

        return tap(new Response(json_decode($response, true), function ($response) {
            return array_key_exists('Error', $response['Response']);
        }, function ($response) {
            return array_key_exists('Error', $response['Response']) ? $response['Response']['Error']['Message'] : null;
        }), function (Response $response) {
            event(new SMSSent($this, $response));
        });
    }

}
