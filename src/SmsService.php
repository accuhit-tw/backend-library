<?php

namespace Accuhit\BackendLibrary;

use Accuhit\BackendLibrary\Exceptions\SmsException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * TODO valid code
 */
class SmsService
{
    private Client $client;

    private string $url;

    private string $account;

    public function __construct()
    {
        $url = env("SMS_URL", "http://sms.accunix.net:9080/api/fet-sms_key.php");
        $this->url = $url;
        $this->account = env("SMS_ACCOUNT", "");

        $path = env("LOG_DIR", sprintf("%s/storage/logs/sms/", $_SERVER['DOCUMENT_ROOT'] ?? '.'));
        $stack = HandlerStack::create();
        $logger = new Logger('Log');
        $logger->pushHandler(new StreamHandler(sprintf($path . 'response_%s.log', date('Y-m-d')), Logger::INFO));
        $stack->push(Middleware::log(
            $logger,
            new MessageFormatter('HttpCode:{code} Method:{method} URI:{uri} Request:{req_body} Response:{res_body} ErrorMsg:{error}')
        ));
        $client = new Client([
            'handler' => $stack,
        ]);
        $this->client = $client;
    }

    /**
     * mock client
     * @param $client
     * @return void
     */
    public function setClient($client): void
    {
        $this->client = $client;
    }

    /**
     * 建立數字驗證碼
     * @param int $length 驗證碼長度
     * @return string
     * @throws SmsException
     */
    public function create(int $length = 4): string
    {
        if ($length < 4 || $length > 10) {
            throw new SmsException("Not supple length");
        }
        return strval(rand(pow(10, $length - 1), pow(10, $length) - 1));
    }

    /**
     * 發送簡訊
     * @param string $phone
     * @param string $msg
     * @param string $platform
     * @return bool
     * @throws SmsException
     * @throws GuzzleException
     */
    public function send(string $phone, string $msg, string $platform): bool
    {
        $hash = hash('sha256', sprintf("%s:sms:%s:%s", $this->account, $phone, $platform));

        $response = $this->client->get($this->url, [
            'query' => [
                'code' => $hash,
                'phone' => $phone,
                'msg' => $msg,
                'platform' => $platform,
            ]
        ]);
        $res = json_decode($response->getBody(), true);

        if ($res['status'] != '00000') {
            throw new SmsException('send sms fail');
        }

        return true;
    }
}
