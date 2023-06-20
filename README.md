# Backend Library

AccuHit 常用套件庫  
[RSS](https://git.accuhit.com.tw/poc/backend-library/-/tags?feed_token=iDazCzWiHM8_zdAzHpRW&format=atom)
## Requirements

| Dependency                                          | Requirement |
|-----------------------------------------------------|-------------|
| [PHP](https://secure.php.net/manual/en/install.php) | `>=8.0.0`   |
| [Guzzle](https://github.com/guzzle/guzzle)          | `>=7.2`     |


## Install
> Install package via [Composer](https://getcomposer.org/).

```shell
composer config repositories.accuhit vcs https://project.sync:ychzCwHzg5s_pyawbAz4@git.accuhit.com.tw/poc/backend-library.git

composer require "accuhit/backend-library:^1.1"
```

## Usage

### Api

| Class      | Class description                                                                                                  |
|------------|--------------------------------------------------------------------------------------------------------------------|
| AccuNixApi | Api function for [accuhit api](https://accucms.accunix.net/login), require line botId and authToken                |
| InvoiceApi | Api function for taiwan [invoice](https://www.einvoice.nat.gov.tw/home/DownLoad?fileName=1510206773173_0.pdf)      |
| LineApi    | Api function for Line [Message Api](https://developers.line.biz/en/docs/messaging-api/) and only for check profile |

#### AccuNixApi  

| Function             | Function description       |
|----------------------|----------------------------|
| setClient            | 設置自定義 GuzzleHttp\Client |
| richMenuSwitch       | 寄送訊息(客製化)              |
| sendMessageByCustom  | 寄送訊息(自訂)                |
| sendMessageByGuid    | 寄送訊息(nix樣板)             |
| addUserInfo          | 寫入好友資訊                  |
| createTag            | 新增標籤                     |
| addTag               | 貼上標籤                     |
| removeTag            | 剝除標籤                     |
| getReferralInfo      | 取得好友推薦目標資訊           |
| referralShareUser    | 取得User推薦好友數            |
| getShareLink         | 取得好友分享連結              |
| getProfile           | 取得好友資訊                 |
| authenticate         | 貼上身份                     |


### InvoiceApi

| Function           | Function description         |
|--------------------|------------------------------|
| getTypeList        | 取得發票條碼類型                 |
| formatPhase        | 發票期別(發票民國年月,年分為民國年,月份必須為雙數月) |
| validNumber        | 驗證發票號碼                       |
| getInvoiceDetails  | 取得電子發票明細                     |

### LineApi

| Function           | Function description         |
|--------------------|------------------------------|
| getProfile         | Get Line user profile        |

#### Example  
```php
<?php

use Accuhit\BackendLibrary\AccuNixApi;
use Accuhit\BackendLibrary\InvoiceApi;
use Accuhit\BackendLibrary\LineApi;

$nix = new AccuNixApi();
/**
 * 取得好友資訊
 * @param string $userToken
 * @param string $options
 * @return array
 * @throws AccuNixException
 */
$profile = $nix->getProfile("user token");

$invoiceApi = new InvoiceApi();
/**
 * 取得電子發票明細
 * @param string $type
 * @param string $invoiceNumber
 * @param string $invoiceDate
 * @param string $randomCode
 * @param string $encrypt
 * @param string $sellerId
 *
 * @return array
 * @throws InvoiceException
 * @throws \InvalidArgumentException
 */
$invoice = $invoiceApi->getInvoiceDetails("Barcode", "AB-12345678", "1990/01/01", "1234");

$lineApi = new LineApi();
/**
 * Get Line user profile
 * @param string $accessToken
 * @return array
 * @discrapt for line response
 * { "userId": "U4af4980629...", "displayName": "Brown", "pictureUrl": "https://profile.line-scdn.net/abcdefghijklmn", "statusMessage": "Hello, LINE!" }
 * @throws \GuzzleHttp\Exception\GuzzleException
 */
$profile = $lineApi->getProfile("user token");
```

### Util

| Class         | Class description                                                                   |
|---------------|-------------------------------------------------------------------------------------|
| UtilJwt       | Util function for JWT Token, sign and validate JWT token                            |
| UtilLogger    | Util function for log, default folder would be `/storage/logs/`                     |
| UtilResponse  | Util function for response, require laravel 9 using `Illuminate\Http\JsonResponse`  |


#### UtilJwt

| Function    | Function description |
|-------------|----------------------|
| getInstance | get instance         |
| encode      | encode data          |
| decode      | decode jwt string    |

#### UtilLogger

| Function | Function description |
|----------|----------------------|
| putLogs  | custom log method    |
| info     | log info             |
| debug    | log debug            |
| warning  | log warning          |
| error    | log error            |


#### UtilResponse  

| Function        | Function description                   |
|-----------------|----------------------------------------|
| toJson          | custom response                        |
| successResponse | success response                       |
| errorResponse   | error response, default error code 400 |


#### Example
```php
<?php

use Accuhit\BackendLibrary\UtilJwt;
use Accuhit\BackendLibrary\UtilLogger;
use Accuhit\BackendLibrary\UtilResponse;

//$token = request()->bearerToken();
$token = $instance->encode(['userId' => 'testuser']);
$instance = UtilJwt::getInstance();
$tokenDecode = $instance->decode($token);

UtilLogger::putLogs("custom");
UtilLogger::info("info");
UtilLogger::debug("debug");
UtilLogger::warning("warning");
UtilLogger::error("error");

UtilResponse::toJson(422, 'params error');
UtilResponse::successResponse('success', ['userId' => 'U12345678']);
UtilResponse::errorResponse('something error');
```

### Service

| Class         | Class description        |
|---------------|--------------------------|
| SmsService    | Service for sending sms  |

#### SmsService

| Function | Function description                           |
|----------|------------------------------------------------|
| create   | create valid code for sms                      |
| send     | send message, require phone, message, platform |


#### Example
```php
<?php

use Accuhit\BackendLibrary\SmsService;

$smsService = new SmsService();
$code = $smsService->create();
$code = $smsService->create(6);
$smsService->send("0900000000", "your message", "accuhit");

```
## Contributing
For details on contributing to this repository, see the [contributing guide](./CONTRIBUTING.md).