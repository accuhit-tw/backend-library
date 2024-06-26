<?php

namespace Accuhit\BackendLibrary;

use Accuhit\BackendLibrary\Exceptions\AccuNixException;
use GuzzleHttp\Client;
use InvalidArgumentException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\MessageFormatter;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

/**
 * @class AccuNixApi
 * @author Alex.hsu
 * please cache AccuNixException for each request.
 */
class AccuNixApi
{
    protected Client $client;
    protected string $apiHost;
    protected string $apiBotHost;
    protected array $headers;
    protected int $timeout;
    protected string $logger;

    public function __construct($botId = null, $authToken = null)
    {
        $path = env("LOG_DIR", sprintf("%s/storage/logs/accunix/", $_SERVER['DOCUMENT_ROOT'] ?? '.'));

        $botId = $botId ?? env('ACCUNIX_LINE_BOT_ID');
        $authToken = $authToken ?? env('ACCUNIX_AUTH_TOKEN');

        $this->timeout = env('GUZZLE_TIMEOUT', 60);

        $stack = HandlerStack::create();
        $logger = new Logger('Log');
        $logger->pushHandler(new StreamHandler(sprintf($path . 'response_%s.log', date('Y-m-d')), Logger::INFO));

        $stack->push(Middleware::log(
            $logger,
            new MessageFormatter('HttpCode:{code} Method:{method} URI:{uri} Request:{req_body} Response:{res_body} ErrorMsg:{error}')
        ));

        $this->client = new Client([
            'timeout' => $this->timeout,
            'handler' => $stack,
        ]);
        $this->apiHost = env('ACCUNIX_URL') . $botId;
        $this->apiBotHost = env('ACCUNIX_BOT_URL') . $botId;
        $this->headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $authToken,
        ];
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
     * 切換主選單
     * @param string $userToken
     * @param string $richmenuGuid
     * @return array
     * @throws AccuNixException
     */
    public function richMenuSwitch(string $userToken, string $richmenuGuid): array
    {
        $uri = "/richmenu/switch";
        $url = $this->apiHost . $uri;
        $params = [
            'userToken' => $userToken,
            'richmenuGuid' => $richmenuGuid,
        ];

        $res = $this->client->post($url, [
            'headers' => $this->headers,
            'json' => $params,
        ]);

        return json_decode($res->getBody()->__toString(), true);
    }

    /**
     * 寄送訊息(客製化)
     * @param string $userToken
     * @param array $messages line format https://developers.line.biz/en/reference/messaging-api/
     * @return array
     * @throws AccuNixException
     */
    public function sendMessageByCustom(string $userToken, array $messages): array
    {
        $params = [
            'messages' => $messages,
            'userToken' => $userToken,
        ];
        return $this->sendMessage($params);
    }

    /**
     * 寄送訊息(nix樣板)
     * @param string $userToken
     * @param string $guid
     * @return array
     * @throws AccuNixException
     */
    public function sendMessageByGuid(string $userToken, string $guid): array
    {
        $params = [
            'userToken' => $userToken,
            'guid' => $guid,
        ];
        return $this->sendMessage($params);
    }

    /**
     * @param array $params
     * @return array
     * @throws AccuNixException
     */
    private function sendMessage(array $params): array
    {
        $uri = "/message/send";
        $url = $this->apiHost . $uri;

        $res = $this->client->post($url, [
            'headers' => $this->headers,
            'json' => $params,
        ]);

        return json_decode($res->getBody()->__toString(), true);
    }

    /**
     * 寫入好友資訊
     * @param string $userToken
     * @param array $data
     * $data = [
     *     "info" => [
     *         "name" => "林艾可",
     *         "birth" => "1990-01-01",
     *         "email" => "email@email.com",
     *         "phone" => "0912345678",
     *         "gender" => "M",
     *         "address" => "台北市松山區敦化南路一段2號5樓"
     *     ],
     *     "customize" => []
     * ];
     *
     * @return array
     * @throws AccuNixException
     */
    public function addUserInfo(string $userToken, array $data): array
    {
        $uri = '/users/data';
        $url = $this->apiHost . $uri;
        $params = [
            'userToken' => $userToken,
            'data' => $data,
        ];

        $res = $this->client->patch($url, [
            'headers' => $this->headers,
            'json' => $params,
        ]);

        return json_decode($res->getBody()->__toString(), true);
    }

    /**
     * 新增標籤
     * @param string $name
     * @param int $days
     * @param string $description
     * @return array
     * @throws InvalidArgumentException
     * @throws AccuNixException
     */
    public function createTag(string $name, int $days, string $description = ''): array
    {
        $uri = '/tag/create';
        $url = $this->apiHost . $uri;
        $params = [
            'name' => $name,
            'days' => $days,
            'description' => $description,
        ];
        if ($days == 0 || $days < -1 || $days > 365) {
            throw new InvalidArgumentException('days must be between 1 and 365 or set -1 to be forever');
        }

        $res = $this->client->post($url, [
            'headers' => $this->headers,
            'json' => $params,
        ]);

        return json_decode($res->getBody()->__toString(), true);
    }

    /**
     * 貼上標籤
     * @param array $userTokens
     * @param array $tags
     * @return array
     * @throws InvalidArgumentException
     * @throws AccuNixException
     */
    public function addTag(array $userTokens, array $tags): array
    {
        if (count($userTokens) > 10 || empty($userTokens)) {
            throw new InvalidArgumentException("users 數量錯誤");
        }

        if (count($tags) > 3 || empty($tags)) {
            throw new InvalidArgumentException("tags 數量錯誤");
        }

        $uri = '/tag/add';
        $url = $this->apiHost . $uri;
        $params = [
            'userTokens' => $userTokens,
            'tags' => $tags
        ];

        $res = $this->client->post($url, [
            'headers' => $this->headers,
            'json' => $params,
        ]);

        return json_decode($res->getBody()->__toString(), true);
    }

    /**
     * 剝除標籤
     * @param array $userTokens
     * @param array $tags
     * @return mixed
     * @throws InvalidArgumentException
     * @throws AccuNixException
     */
    public function removeTag(array $userTokens, array $tags): mixed
    {
        if (count($userTokens) > 10 || empty($userTokens)) {
            throw new InvalidArgumentException("users 數量錯誤");
        }

        if (count($tags) > 3 || empty($tags)) {
            throw new InvalidArgumentException("tags 數量錯誤");
        }

        $uri = '/tag/remove';
        $url = $this->apiHost . $uri;
        $params = [
            'userTokens' => $userTokens,
            'tags' => $tags
        ];

        $res = $this->client->post($url, [
            'headers' => $this->headers,
            'json' => $params,
        ]);

        return json_decode($res->getBody()->__toString(), true);
    }

    /**
     * 取得好友推薦目標資訊
     * @param int $referralId
     * @return array
     * @throws AccuNixException
     */
    public function getReferralInfo(int $referralId): array
    {
        $uri = '/referral/info';
        $url = $this->apiBotHost . $uri;

        $res = $this->client->get($url, [
            'headers' => $this->headers,
            'query' => [
                'referral_id' => $referralId
            ]
        ]);

        return json_decode($res->getBody()->__toString(), true);
    }

    /**
     * 取得User推薦好友數
     * @param string $userToken
     * @param int $referralId
     * @return array
     * @throws AccuNixException
     */
    public function referralShareUser(string $userToken, int $referralId): array
    {
        $uri = '/referral/share-user';
        $url = $this->apiBotHost . $uri;

        $res = $this->client->get($url, [
            'headers' => $this->headers,
            'query' => [
                'user_token' => $userToken,
                'referral_id' => $referralId,
            ]
        ]);

        return json_decode($res->getBody()->__toString(), true);
    }


    /**
     * 取得好友分享連結
     * @param string $userToken
     * @return array
     * @throws AccuNixException
     */
    public function getShareLink(string $userToken): array
    {
        $uri = '/users/getShareLink';
        $url = $this->apiBotHost . $uri;

        $params = [
            'sharer_token' => $userToken,
        ];

        $res = $this->client->post($url, [
            'headers' => $this->headers,
            'json' => $params,
        ]);

        return json_decode($res->getBody()->__toString(), true);
    }

    /**
     * 取得好友資訊
     * @param string $userToken
     * @param string $options
     * @return array
     * @throws AccuNixException
     */
    public function getProfile(string $userToken, string $options = "auth,tags,member,info,customize,referrals"): array
    {
        $uri = '/user/profile';
        $url = $this->apiHost . $uri;

        $res = $this->client->get($url, [
            'headers' => $this->headers,
            'query' => [
                'userToken' => $userToken,
                'options' => $options,
            ]
        ]);

        return json_decode($res->getBody()->__toString(), true);
    }

    /**
     * 貼上身份
     * @param string $userToken
     * @param int $roleId
     * @param array $data
     * @return array
     * @throws AccuNixException
     */
    public function authenticate(string $userToken, int $roleId, array $data = []): array
    {
        $uri = '/authenticate';
        $url = $this->apiHost . $uri;
        $params = [
            'data' => $data,
            'roleId' => $roleId,
            'userToken' => $userToken,
        ];

        $res = $this->client->post($url, [
            'headers' => $this->headers,
            'json' => $params,
        ]);

        return json_decode($res->getBody()->__toString(), true);
    }

    /**
     * 剝除身份
     * @param string $userToken
     * @param int $roleId
     * @throws AccuNixException
     */
    public function authenticateRemove(string $userToken, int $roleId)
    {
        $uri = '/authenticate/role/remove';
        $url = $this->apiHost . $uri;
        $params = [
            'roleId' => $roleId,
            'userToken' => $userToken,
        ];

        $res = $this->client->post($url, [
            'headers' => $this->headers,
            'json' => $params,
        ]);
        
        return json_decode($res->getBody()->__toString(), true);
    }

    /**
     * 貼上身份(使用guid)
     * @param string $userToken
     * @param string $roleGuid
     * @param array $data
     * @return array
     * @throws AccuNixException
     */
    public function authenticateByGuid(string $userToken, string $roleGuid, array $data = []): array
    {
        $uri = '/authenticate';
        $url = $this->apiHost . $uri;
        $params = [
            'data' => $data,
            'guid' => $roleGuid,
            'userToken' => $userToken,
        ];

        $res = $this->client->post($url, [
            'headers' => $this->headers,
            'json' => $params,
        ]);

        return json_decode($res->getBody()->__toString(), true);
    }

    /**
     * 剝除身份(使用guid)
     * @param string $userToken
     * @param string $roleGuid
     * @throws AccuNixException
     */
    public function authenticateRemoveByGuid(string $userToken, string $roleGuid)
    {
        $uri = '/authenticate/role/remove';
        $url = $this->apiHost . $uri;
        $params = [
            'guid' => $roleGuid,
            'userToken' => $userToken,
        ];

        $res = $this->client->post($url, [
            'headers' => $this->headers,
            'json' => $params,
        ]);
        
        return json_decode($res->getBody()->__toString(), true);
    }

    /**
     * 發送票券
     * @param string $userToken
     * @param string $campaignGuid
     * @throws AccuNixException
     */
    public function sendCoupon(string $userToken, string $campaignGuid)
    {
        $uri = '/coupon-campaign/gift';
        $url = $this->apiHost . $uri;
        $params = [
            'campaignGuid' => $campaignGuid,
            'userToken' => $userToken,
        ];

        $res = $this->client->post($url, [
            'headers' => $this->headers,
            'json' => $params,
        ]);

        return json_decode($res->getBody()->__toString(), true);
    }
}
