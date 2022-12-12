<?php

use Accuhit\BackendLibrary\AccuNixApi;
use PHPUnit\Framework\TestCase;

final class AccuNixApiTest extends TestCase
{

    public function testClassConstructor()
    {
        $nix = new AccuNixApi();
        $this->assertNotNull($nix);
    }

    public function testRichMenuSwitch()
    {
        $userToken = env('USER_TOKEN');
        $richmenuGuid = "1837d920a7aHmg";

        $nix = new AccuNixApi();
        $res = $nix->richMenuSwitch($userToken, $richmenuGuid);

    }

    public function testSendMessageByCustomSuccess()
    {
        $userToken = env('USER_TOKEN');
        $message = [
            [
                "text" => "Hello, world",
                "type" => "text"
            ]
        ];

        $nix = new AccuNixApi();
        $res = $nix->sendMessageByCustom($userToken, $message);
        $this->assertEquals('success', $res['message']);
    }

    public function testSendMessageWithGuidSuccess()
    {
        $userToken = env('USER_TOKEN');
        $guid = "1847a245df7D5R";

        $nix = new AccuNixApi();
        $res = $nix->sendMessageByGuid($userToken, $guid);
        $this->assertEquals('success', $res['message']);
    }

    public function testAddUserInfoSuccess()
    {
        $userToken = env('USER_TOKEN');
        $nix = new AccuNixApi();

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
        //TODO mock
        $nix = new AccuNixApi();
        $tagName = 'unitTest';
        $days = 1;
        $description = "unitTest";

//        $res = $nix->createTag($tagName, $days, $description);
//        $this->assertEquals('success', $res['message']);
    }

    public function testAddTag()
    {
        $userToken = env('USER_TOKEN');
        $tag = "unitTest";
        $nix = new AccuNixApi();

        $res = $nix->addTag([$userToken], [$tag]);
        $this->assertEquals('success', $res['message']);
    }

    public function testRemoveTag()
    {
        $userToken = env('USER_TOKEN');
        $tag = "unitTest";
        $nix = new AccuNixApi();

        $res = $nix->removeTag([$userToken], [$tag]);
        $this->assertEquals('success', $res['message']);
    }

    public function testGetReferralInfo()
    {
        $nix = new AccuNixApi();

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
        $userToken = env('USER_TOKEN');
        $nix = new AccuNixApi();

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
        $userToken = env('USER_TOKEN');
        $nix = new AccuNixApi();

        $res = $nix->getShareLink($userToken);
        $this->assertArrayHasKey('message', $res);
        $this->assertIsString($res['message']);
    }

    public function testGetProfile()
    {
        $userToken = env('USER_TOKEN');
        $nix = new AccuNixApi();

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
        $userToken = env('USER_TOKEN');
        $nix = new AccuNixApi();
        $roleId = 604;

        $res = $nix->authenticate($userToken, $roleId);
        $this->assertArrayHasKey('message', $res);
        $this->assertEquals('success', $res['message']);

    }
}