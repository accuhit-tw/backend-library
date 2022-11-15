<?php

namespace Accuhit\BackendLibrary;

use Dotenv\Dotenv;
use ErrorException;
use GuzzleHttp\Client;

class AccuNixApi
{
    protected Client $client;
    protected string $apiHost;
    protected string $apiBotHost;
    protected array $headers;

    public function __construct()
    {
        $dotenv = Dotenv::createImmutable(dirname(__DIR__));
        $dotenv->load();

        $this->client = new Client();
        $this->apiHost = env('ACCUNIX_URL') . env('ACCUNIX_LINEBOTID');
        $this->apiBotHost = env('ACCUNIX_BOT_URL') . env('ACCUNIX_LINEBOTID');
        $this->headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . env('ACCUNIX_AUTHTOKEN'),
        ];
    }

    /**
     * 切換主選單
     * @param string $userToken
     * @param string $richmenuGuid
     * @return array
     * @throws ErrorException
     */
    public function richMenuSwitch(string $userToken, string $richmenuGuid)
    {
        $uri = "/richmenu/switch";
        $url = $this->apiHost . $uri;
        $params = [
            'user_token' => $userToken,
            'richmenuGuid' => $richmenuGuid,
        ];

        $res = $this->client->post($url, [
            'headers' => $this->headers,
            'json' => $params,
        ]);

        if ($res->getStatusCode() != 200) {

        }

        $body = json_decode($res->getBody()->getContents());
        if (!isset($body['message']) && $body['message'] !== 'success') {
            throw new ErrorException($body['message'] ?? "api return blank error message");
        }

        return $body;
    }

    /**
     * 寄送訊息
     * @param string $userToken
     * @param array $messages line format https://developers.line.biz/en/reference/messaging-api/
     * @return array
     * @throws ErrorException
     */
    public function sendMessageByCustom(string $userToken, array $messages = [])
    {
        $params = [
            'messages' => $messages,
            'userToken' => $userToken,
        ];
        return $this->sendMessage($userToken, $params);

    }

    /**
     * 寄送訊息
     * @param string $userToken
     * @param string $guid
     * @return array
     * @throws ErrorException
     */
    public function sendMessageByGuid(string $userToken, string $guid = '')
    {
        $params = [
            'userToken' => $userToken,
            'guid' => $guid,
        ];
        return $this->sendMessage($userToken, $params);
    }

    /**
     * 寄送訊息
     * @param string $userToken
     * @param array $params
     * @return mixed
     * @throws ErrorException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function sendMessage(string $userToken, array $params)
    {
        $uri = "/message/send";
        $url = $this->apiHost . $uri;

        $res = $this->client->post($url, [
            'headers' => $this->headers,
            'json' => $params,
        ]);

        if ($res->getStatusCode() != 202) {

        }

        $body = json_decode($res->getBody()->getContents(), true);
        if (!isset($body['message']) && $body['message'] !== 'success') {
            throw new ErrorException($body['message'] ?? "api return blank error message");
        }

        return $body;
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
     */
    public function addUserInfo(string $userToken, array $data = [])
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

        if ($res->getStatusCode() != 202) {

        }

        $body = json_decode($res->getBody()->getContents(), true);
        if (!isset($body['message']) && $body['message'] !== 'success') {
            throw new ErrorException($body['message'] ?? "api return blank error message");
        }

        return $body;
    }

    /**
     * 新增標籤
     * @param string $name
     * @param int $days
     * @param string $description
     * @return array
     * @throws ErrorException
     */
    public function createTag(string $name, int $days, string $description = '')
    {
        $uri = '/tag/create';
        $url = $this->apiHost . $uri;
        $params = [
            'name' => $name,
            'days' => $days,
            'description' => $description,
        ];
        if ($days == 0 || $days < -1 || $days > 365 ) {
            throw new \ErrorException('days must be between 1 and 365 or set -1 to be forever');
        }

        $res = $this->client->post($url, [
            'headers' => $this->headers,
            'json' => $params,
        ]);

        if ($res->getStatusCode() != 200) {

        }

        $body = json_decode($res->getBody()->getContents(), true);
        if (!isset($body['message']) && $body['message'] !== 'success') {
            throw new ErrorException($body['message'] ?? "api return blank error message");
        }

        return $body;
    }

    /**
     * 貼上標籤
     * @param array $userTokens
     * @param array $tags
     * @return array
     */
    public function addTag(array $userTokens, array $tags)
    {
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

        if ($res->getStatusCode() != 202) {

        }

        $body = json_decode($res->getBody()->getContents(), true);
        if (!isset($body['message']) && $body['message'] !== 'success') {
            throw new ErrorException($body['message'] ?? "api return blank error message");
        }

        return $body;
    }

    /**
     * 剝除標籤
     * @param array $userTokens
     * @param array $tags
     * @return array
     */
    public function removeTag(array $userTokens, array $tags)
    {
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

        if ($res->getStatusCode() != 202) {

        }

        $body = json_decode($res->getBody()->getContents(), true);
        if (!isset($body['message']) && $body['message'] !== 'success') {
            throw new ErrorException($body['message'] ?? "api return blank error message");
        }

        return $body;
    }

    /**
     * 取得好友推薦目標資訊
     * @param int $referralId
     * @return array
     */
    public function getReferralInfo(int $referralId)
    {
        $uri = '/referral/info';
        $url = $this->apiBotHost . $uri;

        $res = $this->client->get($url, [
            'headers' => $this->headers,
            'query' => [
                'referral_id' => $referralId
            ]
        ]);

        if ($res->getStatusCode() != 200) {

        }

        $body = json_decode($res->getBody()->getContents(), true);
        if (!isset($body['message']) && $body['message'] !== 'Success') {
            throw new ErrorException($body['message'] ?? "api return blank error message");
        }

        return $body;
    }

    /**
     * 取得User推薦好友數
     * @param string $userToken
     * @param string $referralId
     * @return array
     */
    public function referralShareUser(string $userToken, int $referralId)
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

        if ($res->getStatusCode() != 200) {

        }

        $body = json_decode($res->getBody()->getContents(), true);
        if (!isset($body['message']) && $body['message'] !== 'Success') {
            throw new ErrorException($body['message'] ?? "api return blank error message");
        }

        return $body;
    }


    /**
     * 取得好友分享連結
     * @param string $userToken
     * @return array
     */
    public function getShareLink(string $userToken)
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

        if ($res->getStatusCode() != 200) {

        }

        $body = json_decode($res->getBody()->getContents(), true);
        if (!isset($body['message'])) {
            throw new ErrorException($body['message'] ?? "api return blank error message");
        }

        return $body;
    }

    /**
     * 取得好友資訊
     * @param string $userToken
     * @param string $options
     * @return array
     */
    public function getProfile(string $userToken, string $options = "auth,tags,member,info,customize,referrals")
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

        if ($res->getStatusCode() != 200) {

        }

        $body = json_decode($res->getBody()->getContents(), true);
        if (isset($body['message'])) {
            throw new ErrorException($body['message'] ?? "api return blank error message");
        }

        return $body;
    }
}
