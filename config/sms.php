<?php

return [
    'default' => 'aliyun',
    'cache' => null,
    'debug' => false,
    'aliyun' => [
        'access_key_id' => env('ALIYUN_ACCESS_KEY_ID', ''),
        'access_key_secret' => env('ALIYUN_ACCESS_KEY_SECRET', ''),
        'sign_name' => '阿里云',
        'scene' => [
            'code' => [
                'template_code' => 'SMS_35650882',
                'template_text' => '您的验证码为：${code}，请勿泄露于他人，本验证码有效期 5 分钟！',
                'sign_name' => '阿里云',
                'rules' => [
                    new \Jundayw\SMS\Rules\Expire('code', null, 5),
                ],
            ],
            'password' => [
                'template_code' => 'SMS_35650882',
                'template_text' => '您的验证码为：${code}，请勿泄露于他人，本验证码有效期 ${minute} 分钟！',
                'rules' => [
                    new \Jundayw\SMS\Rules\Expire('code', 'minute', 5),
                ],
            ],
            'notice' => [
                'template_code' => 'SMS_35650881',
                'template_text' => '体验卡权益已生效，券后立减 ${code} 元！',
            ],
        ],
        'options' => [
            'region_id' => 'cn-hangzhou',
        ],
        'driver' => \Jundayw\SMS\Adapters\AliSMSAdapter::class,
    ],
    'qcloud' => [
        'secret_id' => env('TENCENTCLOUD_SECRET_ID', ''),
        'secret_key' => env('TENCENTCLOUD_SECRET_KEY', ''),
        'sms_sdk_app_id' => env('TENCENTCLOUD_SMS_SDK_APP_ID', ''),
        'sign_name' => '腾讯云',
        'scene' => [
            'code' => [
                'template_id' => '449739',
                'template_text' => '您的验证码为：{1}，请勿泄露于他人，本验证码有效期 5 分钟！！',
                'sign_name' => '腾讯云',
                'rules' => [
                    new \Jundayw\SMS\Rules\Expire(1, null, 5),
                ],
            ],
            'password' => [
                'template_id' => '449739',
                'template_text' => '您的验证码为：{1}，请勿泄露于他人，本验证码有效期 {2} 分钟！！',
                'rules' => [
                    new \Jundayw\SMS\Rules\Expire(1, 2, 5),
                ],
            ],
            'notice' => [
                'template_id' => '449738',
                'template_text' => '体验卡权益已生效，券后立减 {1} 元！',
            ],
        ],
        'options' => [
            'region' => 'ap-guangzhou',
        ],
        'driver' => \Jundayw\SMS\Adapters\QCloudSMSAdapter::class,
    ],
];
