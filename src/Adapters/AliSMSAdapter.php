<?php

namespace Jundayw\SMS\Adapters;

use AlibabaCloud\SDK\Dysmsapi\V20170525\Dysmsapi;
use AlibabaCloud\SDK\Dysmsapi\V20170525\Models\SendSmsRequest;
use AlibabaCloud\Tea\Utils\Utils\RuntimeOptions;
use Darabonba\OpenApi\Models\Config;
use Illuminate\Support\Facades\Log;
use Jundayw\SMS\Contracts\SMSResponseContract;
use Jundayw\SMS\Events\SMSSent;
use Jundayw\SMS\Exceptions\SMSException;
use Jundayw\SMS\Response\Response;

class AliSMSAdapter extends SMSAdapter
{
    private Dysmsapi $client;

    protected function initialize(): void
    {
        $config = new Config([
            // 必填，您的 AccessKey ID
            "accessKeyId" => $this->getOptions('access_key_id'),
            // 必填，您的 AccessKey Secret
            "accessKeySecret" => $this->getOptions('access_key_secret'),
        ]);
        // 访问的域名
        $config->endpoint = "dysmsapi.aliyuncs.com";
        $this->client     = new Dysmsapi($config);;
    }

    /**
     * @param mixed $options
     * @return SMSResponseContract
     */
    public function send(mixed $options = []): SMSResponseContract
    {
        if (confi('sms.develop')) {
            return parent::send();
        }

        try {
            $data = [
                "phoneNumbers" => implode(',', $this->getPhoneNumbers()),
                "signName" => $this->getTemplate('sign_name') ?? $this->getOptions('sign_name'),
                "templateCode" => $this->getTemplate('template_code'),
                "templateParam" => json_encode($this->getTemplateParam(), JSON_UNESCAPED_UNICODE),
            ];
            debug_sms('debug')(__METHOD__, $data);
            $response = $this->client->sendSmsWithOptions(new SendSmsRequest($data), new RuntimeOptions([
                "ignoreSSL" => true,
            ]));
        } catch (\Throwable $exception) {
            throw new SMSException($exception->getMessage());
        }
        debug_sms('debug')(__METHOD__, $response->body->toMap());
        // $response = '{
        //     "Code": "OK",
        //     "Message": "OK",
        //     "BizId": "9006197469364984****",
        //     "RequestId": "F655A8D5-B967-440B-8683-DAD6FF8DE990"
        // }';
        // $response = '{
        //     "Message": "签名不支持修改",
        //     "RequestId": "********-****-****-****-************",
        //     "Code": "isv.ERROR_SIGN_NOT_MODIFY"
        // }';
        return tap(new Response($response->body->toMap(), function ($response) {
            return $response['Code'] != 'OK';
        }, function ($response) {
            return $response['Message'] ?? null;
        }), function (Response $response) {
            event(new SMSSent($this, $response));
        });
    }
}
