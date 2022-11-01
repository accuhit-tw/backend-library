<?php

namespace Accuhit\BackendLibrary;

use Exception;
use GuzzleHttp\Client;

class AccuNixApi
{
    protected $client;
    protected $api_host;
    protected $headers;

    public function __construct()
    {
        $this->client = new Client();
        $this->api_host = "https://api-tf.accunix.net/api/LINEBot/" . config('app.accuNixLINEBotId');
        $this->headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . config('app.accuNixAuthToken')
        ];
    }

    /** 身份驗證
     * @throws Exception
     */
    public function authenticate(string $userToken, string $roleId, array $data = [])
    {
        $uri = '/authenticate';
        $result = $this->curl('POST', $uri, compact('userToken', 'roleId', 'data'));
        if (isset($result->message) && $result->message !== 'Success') {
            throw new Exception($result->message ?? "api return blank error message");
        }
        return $result;
    }

    /** 寄送訊息
     * @throws Exception
     */
    public function sendMessage(string $userToken, array $messages = [], int $messageId)
    {
        $json = compact('userToken', 'messages');
        if ($messageId != 0) {
            $json = compact('userToken', 'messageId');
        }
        $uri = '/message/send';
        $result = $this->curl('POST', $uri, $json);
        if (isset($result->message) && $result->message !== 'Success') {
            throw new Exception($result->message ?? "api return blank error message");
        }
        return $result;
    }

    /** 寫入好友資訊
     * @throws Exception
     */
    public function addUserInfo(string $userToken, array $data = [])
    {
        $uri = '/users/data';
        $result = $this->curl('PUT', $uri, compact('userToken', 'data'));
        if (isset($result->message) && $result->message !== 'Success') {
            throw new Exception($result->message ?? "api return blank error message");
        }

        return $result;
    }

    /** 新增標籤
     * @throws Exception
     */
    public function tagCreate(string $name, string $description, int $days)
    {
        $uri = '/tag/create';
        $result = $this->curl('POST', $uri, [
            'headers' => $this->headers,
            'json' => compact('name', 'description', 'days')
        ]);
        if (isset($result->message) && $result->message !== 'Success') {
            throw new Exception($result->message ?? "api return blank error message");
        }
        return $result;
    }

    /** 取得好友推薦目標資訊
     * @throws Exception
     */
    public function getReferralInfo(string $referralId)
    {
        $uri = 'referral/info?referral_id=' . $referralId;
        $result = $this->curl('GET', $uri, [
            'headers' => $this->headers
        ]);
        if (isset($result->message) && $result->message !== 'Success') {
            throw new Exception($result->message ?? "api return blank error message");
        }
        return $result;
    }

    /** 好友貼標
     * @throws Exception
     */
    public function tagAdd(array $userTokens, array $tags)
    {
        $uri = '/tag/add';
        $result = $this->curl('POST', $uri, compact('userTokens', 'tags'));
        if (isset($result->message) && $result->message !== 'Success') {
            throw new Exception($result->message ?? "api return blank error message");
        }
        return $result;
    }

    /** 取得User推薦好友數
     * @throws Exception
     */
    public function referralShareUser(string $user_token, string $referral_id)
    {
        $uri = '/referral/share-user';
        $result = $this->curl('GET', $uri, compact('user_token', 'referral_id'));
        if (isset($result->message) && $result->message !== 'Success') {
            throw new Exception($result->message ?? "api return blank error message");
        }
        return $result;
    }

    /** 取得好友分享連結
     * @throws Exception
     */
    public function getShareLink(string $sharer_token)
    {
        $uri = '/users/getShareLink';
        $result = $this->curl('POST', $uri, compact('sharer_token'));
        if (isset($result->message) && $result->message !== 'Success') {
            throw new Exception($result->message ?? "api return blank error message");
        }
        return $result;
    }

    /** 取得好友資訊
     * @throws Exception
     */
    public function getUserProfile(string $user_token, string $options)
    {
        $uri = '/users/getUserProfile';
        $result = $this->curl('GET', $uri, compact('user_token', 'options'));
        if (isset($result->message) && $result->message !== '') {
            throw new Exception($result->message ?? "api return blank error message");
        }
        return $result;
    }

    /**
     * curl
     */
    private function curl(string $method, string $uri, array $fields = [])
    {
        $url = $this->api_host . $uri;
        $curl = curl_init();
        $opts = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => json_encode($fields),
            CURLOPT_HTTPHEADER => $this->headers,
        );
        curl_setopt_array($curl, $opts);
        $response = json_decode(curl_exec($curl));
        curl_close($curl);
        UtilLogger::putLogs("accuNix", print_r(["url" => $url, "body" => $opts, "res" => $response], true));
        return $response;
    }
}
