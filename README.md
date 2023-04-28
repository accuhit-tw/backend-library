# Backend Library

AccuHit 常用套件庫

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
| AccuNixApi | Api function for accuhit api, require line botId and authToken                                                     |
| InvoiceApi | Api function for taiwan [invoice](https://www.einvoice.nat.gov.tw/home/DownLoad?fileName=1510206773173_0.pdf)      |
| LineApi    | Api function for Line [Message Api](https://developers.line.biz/en/docs/messaging-api/) and only for check profile |

### Util


| Class         | Class description                                                                   |
|---------------|-------------------------------------------------------------------------------------|
| UtilJwt       | Util function for JWT Token, sign and validate JWT token                            |
| UtilLogger    | Util function for log, default folder would be `/storage/logs/`                     |
| UtilResponse  | Util function for response, require laravel 9 using `Illuminate\Http\JsonResponse`  |
