<?php

namespace Accuhit\BackendLibrary;

use GuzzleHttp\Client;

class LineApi
{
    public string $url;
    private Client $client;

    public function __construct(Client $client = null)
    {
        $this->url = 'https://api.line.me';

        $this->client = $client ?? new Client();
    }

    /**
     * Get Line user profile
     * @param string $accessToken
     * @return array
     * @discrapt for line response
     * { "userId": "U4af4980629...", "displayName": "Brown", "pictureUrl": "https://profile.line-scdn.net/abcdefghijklmn", "statusMessage": "Hello, LINE!" }
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getProfile(string $accessToken)
    {
        $uri = $this->url . '/v2/profile';

        $headers = [
            'Authorization' => 'Bearer '. $accessToken,
        ];
        $response = $this->client->get($uri, [
            'headers' => $headers,
        ]);

        return json_decode($response->getBody(), true);
    }
}
