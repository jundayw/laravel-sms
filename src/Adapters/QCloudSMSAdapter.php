<?php

namespace Jundayw\SMS\Adapters;

use Jundayw\SMS\Contracts\SMSResponseContract;
use Jundayw\SMS\Events\SMSSent;
use Jundayw\SMS\Exceptions\SMSException;
use Jundayw\SMS\Response\Response;
use TencentCloud\Common\Credential;
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;
use TencentCloud\Sms\V20210111\Models\SendSmsRequest;
use TencentCloud\Sms\V20210111\SmsClient;

class QCloudSMSAdapter extends SMSAdapter
{
    private SmsClient $client;

    protected function initialize(): void
    {
        $credential  = new Credential($this->getOptions('secret_id'), $this->getOptions('secret_key'));
        $httpProfile = new HttpProfile();
        $httpProfile->setEndpoint("sms.tencentcloudapi.com");

        $clientProfile = new ClientProfile();
        $clientProfile->setHttpProfile($httpProfile);

        $this->client = new SmsClient($credential, $this->getOptions('options')['region'], $clientProfile);
    }

    public function scene(string $templateName, array $templateParam = []): static
    {
        parent::scene($templateName, $templateParam);
        $i      = 1;
        $params = [];
        foreach ($this->templateParam as $param) {
            $params[$i++] = (string)$param;
        }
        $this->templateParam = $params;
        return $this;
    }

    public function send(mixed $options = []): SMSResponseContract
    {
        if (config('sms.develop')) {
            return parent::send();
        }

        $data = [
            "PhoneNumberSet" => $this->getPhoneNumbers(),
            "SmsSdkAppId" => $this->getOptions('sms_sdk_app_id'),
            "SignName" => $this->getTemplate('sign_name') ?? $this->getOptions('sign_name'),
            "TemplateId" => $this->getTemplate('template_id'),
            "TemplateParamSet" => array_values($this->getTemplateParam()),
        ];
        debug_sms('debug')(__METHOD__, $data);
        try {
            $request = new SendSmsRequest();
            $request->fromJsonString(json_encode($data));
            $response = $this->client->SendSms($request);
        } catch (\Throwable $exception) {
            throw new SMSException($exception->getMessage());
        }
        $response = json_decode($response->toJsonString(), true);
        debug_sms('debug')(__METHOD__, $response);
        // $response = '{
        //     "Error": {
        //         "Code": "AuthFailure.SignatureFailure",
        //         "Message": "The provided credentials could not be validated. Please check your signature is correct."
        //     },
        //     "RequestId": "ed93f3cb-f35e-473f-b9f3-0d451b8b79c6"
        // }';
        // $response = '{
        //     "InstanceStatusSet": [],
        //     "RequestId": "b5b41468-520d-4192-b42f-595cc34b6c1c"
        // }';
        return tap(new Response($response, function ($response) {
            return array_key_exists('Error', $response);
        }, function ($response) {
            return array_key_exists('Error', $response) ? $response['Error']['Message'] : null;
        }), function (Response $response) {
            event(new SMSSent($this, $response));
        });
    }
}
