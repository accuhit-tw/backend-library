<?php

namespace Accuhit\Tests;

use Accuhit\BackendLibrary\LineApi;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

final class LineApiTest extends TestCase
{

    public function testGetProfile()
    {
        $json = '{ "userId": "U4af4980629...", "displayName": "Brown", "pictureUrl": "https://profile.line-scdn.net/abcdefghijklmn", "statusMessage": "Hello, LINE!" }';
        $mock = new MockHandler([
            new Response(200, [], $json),
        ]);
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $lineApi = new LineApi($client);
        $accessToken = env('LINE_ACCESS_TOKEN');
        $profile = $lineApi->getProfile($accessToken);
        $this->assertArrayHasKey('userId', $profile);
        $this->assertArrayHasKey('displayName', $profile);
        $this->assertArrayHasKey('pictureUrl', $profile);
        $this->assertArrayHasKey('statusMessage', $profile);
    }
}
