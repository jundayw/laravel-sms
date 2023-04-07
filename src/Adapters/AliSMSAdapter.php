<?php

namespace Jundayw\SMS\Adapters;

use Jundayw\SMS\Contracts\SMSResponseContract;
use Jundayw\SMS\Events\SMSSent;
use Jundayw\SMS\Response\Response;

class AliSMSAdapter extends SMSAdapter
{
    public function send(mixed $options = []): SMSResponseContract
    {
        $response = '{
  "Code": "OK",
  "Message": "OK",
  "BizId": "9006197469364984****",
  "RequestId": "F655A8D5-B967-440B-8683-DAD6FF8DE990"
}';
//         $response = '{
//     "Message": "签名不支持修改",
//     "RequestId": "********-****-****-****-************",
//     "Code": "isv.ERROR_SIGN_NOT_MODIFY"
// }';
        return tap(new Response(json_decode($response, true), function ($response) {
            return $response['Code'] != 'OK';
        }, function ($response) {
            return $response['Message'] ?? null;
        }), function (Response $response) {
            event(new SMSSent($this, $response));
        });
    }
}
