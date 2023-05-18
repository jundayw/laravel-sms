# 安装方法

命令行下, 执行 composer 命令安装:

````
composer require jundayw/laravel-sms
````

# 对象方法

## 短信服务商

public function via(string $via): SMSAdapterContract;

```php
$this->via('aliyun');
```

## 短信发送对象

public function to(mixed $recipients): static;

```php
$this->to('13600000000');
$this->to('13600000000,13600000001');
$this->to(['13600000000','13600000001']);
```

## 短信名称及短信参数

public function scene(string $templateName, array $templateParam = []): static;

```php
$this->scene('code');
$this->scene('code',['code' => 123456]);
```

## 发送短信

public function send(array $options = []): SMSResponseContract;

```php
$this->send();
```

## 短信验证

public function check(mixed $input): bool;

```php
$this->to('13600000000')->scene('code')->check('123456');
```

# 使用场景

## 发布配置文件

```php
php artisan vendor:publish --tag=laravel-sms-config
```

## 配置文件

```php
return [
    'default' => 'aliyun',
    'cache' => null,
    'aliyun' => [
        'access_key_id' => env('ALIYUN_ACCESS_KEY_ID', ''),
        'access_key_secret' => env('ALIYUN_ACCESS_KEY_SECRET', ''),
        'sign_name' => '阿里云',
        'scenes' => [
            'code' => [
                'template_code' => 'SMS_35650882',
                'template_text' => '您的验证码为：${code}，请勿泄露于他人，本验证码有效期 5 分钟！',
                'sign_name' => '阿里云',
                'hooks' => [
                    new \Jundayw\SMS\Hooks\Expire('code', null, 5),
                ],
            ],
            'password' => [
                'template_code' => 'SMS_35650882',
                'template_text' => '您的验证码为：${code}，请勿泄露于他人，本验证码有效期 ${minute} 分钟！',
                'hooks' => [
                    new \Jundayw\SMS\Hooks\Expire('code', 'minute', 5),
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
        'scenes' => [
            'code' => [
                'template_id' => '449739',
                'template_text' => '您的验证码为：{1}，请勿泄露于他人，本验证码有效期 5 分钟！！',
                'sign_name' => '腾讯云',
                'hooks' => [
                    new \Jundayw\SMS\Hooks\Expire(1, null, 5),
                ],
            ],
            'password' => [
                'template_id' => '449739',
                'template_text' => '您的验证码为：{1}，请勿泄露于他人，本验证码有效期 {2} 分钟！！',
                'hooks' => [
                    new \Jundayw\SMS\Hooks\Expire(1, 2, 5),
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
```

```php
SMS::via('aliyun')->to('13600000000,13600000001')->scene('code', ['code' => 123456])->send();
SMS::via('aliyun')->to('13600000000')->scene('code')->check('123456');

SMS::via('aliyun')->to('13600000001')->scene('password', ['code' => 123456, 'minute' => 15])->send();
sms()->via('aliyun')->to(['13600000001'])->scene('password')->check(123456);

SMS::via('qcloud')->to(['13600000000','13600000001'])->scene('code', ['code' => 123456])->send();
SMS::via('qcloud')->to(['13600000001'])->scene('code')->check(123456);

SMS::via('qcloud')->to('13627685922')->scene('password', ['code' => 123456, 'minute' => 15])->send();
sms()->via('qcloud')->to('13600000001')->scene('password')->check(123456);
```

```php
SMS::to('13600000000,13600000001')->scene('code', ['code' => 123456])->send();
SMS::via('qcloud')->to(['13600000000','13600000001'])->scene('code', ['code' => 123456])->send();

sms()->to(['13600000001'])->scene('password')->check(123456);
sms()->via('qcloud')->to('13600000001')->scene('password')->check(123456);
```