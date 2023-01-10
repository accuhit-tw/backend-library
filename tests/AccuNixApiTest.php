<?php

use Accuhit\BackendLibrary\AccuNixApi;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

final class AccuNixApiTest extends TestCase
{

    public function testClassConstructor()
    {
        $nix = new AccuNixApi();
        $this->assertNotNull($nix);
    }

//    public function testRichMenuSwitch()
//    {
//        $userToken = env('USER_TOKEN');
//        $richmenuGuid = "1837d920a7aHmg";
//
//        $nix = new AccuNixApi();
//        $res = $nix->richMenuSwitch($userToken, $richmenuGuid);
//
//    }

    public function testSendMessageByCustomSuccess()
    {
        $json = <<<JSON
{
    "message": "success"
}
JSON;
        $mock = new MockHandler([
            new Response(200, [], $json),
        ]);
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $userToken = env('USER_TOKEN');
        $message = [
            [
                "text" => "Hello, world",
                "type" => "text"
            ]
        ];

        $nix = new AccuNixApi();
        $nix->setClient($client);
        $res = $nix->sendMessageByCustom($userToken, $message);
        $this->assertEquals('success', $res['message']);
    }

    public function testSendMessageWithGuidSuccess()
    {
        $json = <<<JSON
{
    "message": "success"
}
JSON;
        $mock = new MockHandler([
            new Response(200, [], $json),
        ]);
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $userToken = env('USER_TOKEN');
        $guid = "1847a245df7D5R";

        $nix = new AccuNixApi();
        $nix->setClient($client);
        $res = $nix->sendMessageByGuid($userToken, $guid);
        $this->assertEquals('success', $res['message']);
    }

    public function testAddUserInfoSuccess()
    {
        $json = <<<JSON
{
    "message": "success"
}
JSON;
        $mock = new MockHandler([
            new Response(200, [], $json),
            new Response(200, [], $json),
            new Response(200, [], $json),
        ]);
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $userToken = env('USER_TOKEN');
        $nix = new AccuNixApi();
        $nix->setClient($client);

        $data = [];
        $res = $nix->addUserInfo($userToken, $data);
        $this->assertEquals('success', $res['message']);

        $data = [];
        $data['info'] = [
            'name' => "林艾可",
            'birth' => "1990-01-01",
            'email' => "email@email.com",
            'phone' => "0912345678",
            'address' => "台北市松山區敦化南路一段2號5樓",
            'gender' => "M",
        ];
        $res = $nix->addUserInfo($userToken, $data);
        $this->assertEquals('success', $res['message']);

        $data['info'] = [
            'name' => "林艾可",
            'birth' => "1990-01-01",
            'phone' => "0912345678",
            'gender' => "M",
        ];
        $data['customize'] = [
            'key-1',
            'key-2',
            'key-2',
            'isDate' => [
                'type' => 'date',
                'value' => '2022-08-02',
            ],
        ];
        $res = $nix->addUserInfo($userToken, $data);
        $this->assertEquals('success', $res['message']);

    }

    public function testCreateTag()
    {
        $json = <<<JSON
{
    "message": "success"
}
JSON;
        $mock = new MockHandler([
            new Response(200, [], $json),
        ]);
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $nix = new AccuNixApi();
        $nix->setClient($client);
        $tagName = 'unitTest';
        $days = 1;
        $description = "unitTest";

        $res = $nix->createTag($tagName, $days, $description);
        $this->assertEquals('success', $res['message']);
    }

    public function testAddTag()
    {
        $json = <<<JSON
{
    "message": "success"
}
JSON;
        $mock = new MockHandler([
            new Response(200, [], $json),
        ]);
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $userToken = env('USER_TOKEN');
        $tag = "unitTest";
        $nix = new AccuNixApi();
        $nix->setClient($client);

        $res = $nix->addTag([$userToken], [$tag]);
        $this->assertEquals('success', $res['message']);
    }

    public function testRemoveTag()
    {
        $json = <<<JSON
{
    "message": "success"
}
JSON;
        $mock = new MockHandler([
            new Response(200, [], $json),
        ]);
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $userToken = env('USER_TOKEN');
        $tag = "unitTest";
        $nix = new AccuNixApi();
        $nix->setClient($client);

        $res = $nix->removeTag([$userToken], [$tag]);
        $this->assertEquals('success', $res['message']);
    }

    public function testGetReferralInfo()
    {
        $json = <<<JSON
{
    "data": {
        "name": "推薦好友目標",
        "end_at": "2021-01-22 18:52:00",
        "start_at": "2021-01-13 18:52:00",
        "is_active": 0,
        "created_at": "2021-01-13 18:52:32",
        "updated_at": "2021-01-14 18:13:56",
        "description": "說明",
        "total_share_count": 0
    },
    "message": "Success"
}
JSON;
        $mock = new MockHandler([
            new Response(200, [], $json),
        ]);
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $nix = new AccuNixApi();
        $nix->setClient($client);

        $id = 347;
        $res = $nix->getReferralInfo($id);

        $this->assertIsArray($res['data']);
        $this->assertArrayHasKey('name', $res['data']);
        $this->assertArrayHasKey('end_at', $res['data']);
        $this->assertArrayHasKey('start_at', $res['data']);
        $this->assertArrayHasKey('is_active', $res['data']);
        $this->assertArrayHasKey('created_at', $res['data']);
        $this->assertArrayHasKey('updated_at', $res['data']);
        $this->assertArrayHasKey('description', $res['data']);
        $this->assertArrayHasKey('total_share_count', $res['data']);
        $this->assertEquals('Success', $res['message']);
    }

    public function testReferralShareUser()
    {
        $json = <<<JSON
{
    "data": {
        "name": "NAME",
        "picture": "url",
        "share_count": 0
    },
    "message": "Success"
}
JSON;
        $mock = new MockHandler([
            new Response(200, [], $json),
        ]);
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $userToken = env('USER_TOKEN');
        $nix = new AccuNixApi();
        $nix->setClient($client);

        $id = 347;
        $res = $nix->referralShareUser($userToken, $id);

        $this->assertIsArray($res['data']);
        $this->assertArrayHasKey('name', $res['data']);
        $this->assertArrayHasKey('picture', $res['data']);
        $this->assertArrayHasKey('share_count', $res['data']);

        $this->assertEquals('Success', $res['message']);
    }

    public function testGetShareLink()
    {
        $json = <<<JSON
{
    "message": "{{ShareLink}}"
}
JSON;
        $mock = new MockHandler([
            new Response(200, [], $json),
        ]);
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $userToken = env('USER_TOKEN');
        $nix = new AccuNixApi();
        $nix->setClient($client);

        $res = $nix->getShareLink($userToken);
        $this->assertArrayHasKey('message', $res);
        $this->assertIsString($res['message']);
    }

    public function testGetProfile()
    {
        $json = <<<JSON
{
    "info": {
        "name": "Karen lin",
        "birth": "1994-06-09",
        "email": "sunnywei060@gmail.com",
        "phone": "0912345678",
        "gender": "F",
        "address": "台北市士林區"
    },
    "tags": [
        {
            "id": 28,
            "name": "為客服評分",
            "active_count": 2
        },
        {
            "id": 65,
            "name": "口罩",
            "active_count": 25
        },
        {
            "id": 117,
            "name": "關鍵字測試",
            "active_count": 17
        }
    ],
    "member": {
        "phone": "0952014158",
        "card_id": "abBc12345678@abBc12345678@abBc12345678@",
        "is_member": true
    },
    "customize": {
        "hobby": "游泳",
        "key-1": "string-1",
        "key-2": "string-2",
        "model": "花車",
        "occupation": "PM",
        "orderedDate": "2018-05-01",
        "orderedTime": "19:20",
        "tttttttttttttttttttt": "kk"
    },
    "referrals": [
        {
            "id": 6,
            "name": "達標1,5",
            "end_at": null,
            "targets": [
                {
                    "target": 5,
                    "achieved_at": null
                },
                {
                    "target": 1,
                    "achieved_at": "2021-10-24 19:53:09"
                },
                {
                    "target": "4",
                    "achieved_at": null
                }
            ],
            "start_at": null,
            "join_users": [
                {
                    "name": "WiwiBaby 微微女孩",
                    "join_at": "2021-10-24 19:53:08",
                    "picture": "https://sprofile.line-scdn.net/0hlCSQ3seqM2x-KCW3GehNEw54MAZdWWp-UEp8Wh8oalkRGXc5WkcvDBsrOVQXTyQ7UBp-Xkt4bg9yO0QKYH7PWHkYbVtCG3M_VEZ5jw",
                    "user_token": "U85a529e0329f96740d973e63738c6f31"
                }
            ]
        }
    ],
    "authentication": [
        {
            "id": 23,
            "name": "車款",
            "roles": [
                {
                    "id": 65,
                    "name": "粉車"
                }
            ]
        },
        {
            "id": 48,
            "name": "LEVEL",
            "roles": [
                {
                    "id": 103,
                    "name": "LEVEL 2"
                }
            ]
        }
    ]
}
JSON;
        $mock = new MockHandler([
            new Response(200, [], $json),
        ]);
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $userToken = env('USER_TOKEN');
        $nix = new AccuNixApi();
        $nix->setClient($client);

        $res = $nix->getProfile($userToken);
        $this->assertArrayHasKey('info', $res);
        $this->assertArrayHasKey('tags', $res);
        $this->assertArrayHasKey('member', $res);
        $this->assertArrayHasKey('customize', $res);
        $this->assertArrayHasKey('referrals', $res);
        $this->assertArrayHasKey('authentication', $res);
    }

    public function testAuthenticate()
    {
        $json = <<<JSON
{
    "message": "success"
}
JSON;
        $mock = new MockHandler([
            new Response(200, [], $json),
        ]);
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        $userToken = env('USER_TOKEN');
        $nix = new AccuNixApi();
        $nix->setClient($client);
        $roleId = 604;

        $res = $nix->authenticate($userToken, $roleId);
        $this->assertArrayHasKey('message', $res);
        $this->assertEquals('success', $res['message']);

    }
}