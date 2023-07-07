<?php

namespace Accuhit\Tests;

use Accuhit\BackendLibrary\AccuNixApi;
use Accuhit\BackendLibrary\Exceptions\AccuNixException;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;

final class AccuNixApiTest extends TestCase
{

    public function testClassConstructor()
    {
        $nix = new AccuNixApi();
        $this->assertNotNull($nix);
    }

    public function testRichMenuSwitchSuccess()
    {
        // Arrange
        $userToken = 'USER_TOKEN';
        $richmenuGuid = 'RICHMENU_GUID';
        $expectedResult = [
            'message' => 'success',
        ];

        // Mock the HTTP client and response
        $httpClientMock = $this->createMock(Client::class);
        $httpClientMock->expects($this->once())
            ->method('post')
            ->willReturn(new Response(200, [], json_encode($expectedResult)));
        $nix = new AccuNixApi();
        $nix->setClient($httpClientMock);

        // Act
        $result = $nix->richMenuSwitch($userToken, $richmenuGuid);

        // Assert
        $this->assertEquals($expectedResult, $result);
    }

    public function testRichMenuSwitchWith422Error()
    {
        // Arrange
        $userToken = 'USER_TOKEN';
        $richmenuGuid = 'RICHMENU_GUID';
        $expectedResult = [
            'message' => '輸入數值錯誤',
        ];

        // Mock the HTTP client and response
        $mockClient = $this->createMock(Client::class);
        $mockClient->expects($this->once())
            ->method('post')
            ->willThrowException(
                new AccuNixException('Unprocessable Entity',
                    new Request('POST', 'https://api-tf.accunix.net/api/line/{botid}/richmenu/switch'),
                    new Response(422, [], json_encode($expectedResult)))
            );
        $nix = new AccuNixApi();
        $nix->setClient($mockClient);

        // Assert
        $this->expectException(AccuNixException::class);
        $this->expectExceptionMessage('Unprocessable Entity');

        // Act
        $nix->richMenuSwitch($userToken, $richmenuGuid);
    }

    public function testSendMessageByCustomSuccess()
    {
        // Arrange
        $userToken = 'USER_TOKEN';
        $message = [
            [
                "text" => "Hello, world",
                "type" => "text"
            ]
        ];

        $expectedResult = [
            'message' => 'success',
        ];

        $mockClient = $this->createMock(Client::class);
        $mockClient->expects($this->once())
            ->method('post')
            ->willReturn(new Response(200, [], json_encode($expectedResult)));
        $nix = new AccuNixApi();
        $nix->setClient($mockClient);

        // Act
        $result = $nix->sendMessageByCustom($userToken, $message);

        // Assert
        $this->assertEquals($expectedResult, $result);
        $this->assertEquals('success', $result['message']);
    }

    public function testSendMessageByCustomWith422Error()
    {
        // Arrange
        $userToken = 'USER_TOKEN';
        $message = [
            [
                "text" => "Hello, world",
                "type" => "text"
            ]
        ];
        $expectedResult = [
            'message' => 'error: Invalid user_token',
        ];

        // Mock the HTTP client and response
        $mockClient = $this->createMock(Client::class);
        $mockClient->expects($this->once())
            ->method('post')
            ->willThrowException(
                new AccuNixException('Unprocessable Entity',
                    new Request('POST', 'https://api-tf.accunix.net/api/line/{botid}/richmenu/switch'),
                    new Response(422, [], json_encode($expectedResult)))
            );
        $nix = new AccuNixApi();
        $nix->setClient($mockClient);

        // Assert
        $this->expectException(AccuNixException::class);
        $this->expectExceptionMessage('Unprocessable Entity');

        // Act
        $nix->sendMessageByCustom($userToken, $message);
    }

    public function testSendMessageWithGuidSuccess()
    {
        // Arrange
        $userToken = 'USER_TOKEN';
        $guid = "GUID";

        $expectedResult = [
            'message' => 'success',
        ];

        $mockClient = $this->createMock(Client::class);
        $mockClient->expects($this->once())
            ->method('post')
            ->willReturn(new Response(200, [], json_encode($expectedResult)));
        $nix = new AccuNixApi();
        $nix->setClient($mockClient);

        // Act
        $res = $nix->sendMessageByGuid($userToken, $guid);

        // Assert
        $this->assertEquals($expectedResult, $res);
        $this->assertEquals('success', $res['message']);
    }

    public function testSendMessageWithGuidWith422Error()
    {
        // Arrange
        $userToken = 'USER_TOKEN';
        $guid = "GUID";

        //文件僅條列此錯誤
        $expectedResult = [
            'message' => 'error: Invalid user_token',
        ];

        $mockClient = $this->createMock(Client::class);
        $mockClient->expects($this->once())
            ->method('post')
            ->willThrowException(
                new AccuNixException('Unprocessable Entity',
                    new Request('POST', 'https://api-tf.accunix.net/api/line/{botid}/message/send'),
                    new Response(422, [], json_encode($expectedResult)))
            );
        $nix = new AccuNixApi();
        $nix->setClient($mockClient);

        // Assert
        $this->expectException(AccuNixException::class);
        $this->expectExceptionMessage('Unprocessable Entity');

        // Act
        $nix->sendMessageByGuid($userToken, $guid);
    }

    public static function userInfoProvider(): array
    {
        return [
            [
                json_encode([])
            ],
            [
                json_encode([
                    'info' => [
                        'name' => "林艾可",
                        'birth' => "1990-01-01",
                        'email' => "email@email.com",
                        'phone' => "0912345678",
                        'address' => "台北市松山區敦化南路一段2號5樓",
                        'gender' => "M",
                    ],
                ])
            ],
            [
                json_encode([
                    'info' => [
                        'name' => "林艾可",
                        'birth' => "1990-01-01",
                        'email' => "email@email.com",
                        'phone' => "0912345678",
                        'address' => "台北市松山區敦化南路一段2號5樓",
                        'gender' => "M",
                    ],
                    'customize' => [
                        'key-1',
                        'key-2',
                        'key-2',
                        'isDate' => [
                            'type' => 'date',
                            'value' => '2022-08-02',
                        ],
                    ],
                ])
            ],
        ];
    }

    /**
     *
     * @dataProvider userInfoProvider
     */
    public function testAddUserInfoSuccess(string $data)
    {
        // Arrange
        $userToken = 'USER_TOKEN';
        $expectedResult = [
            'message' => 'success',
        ];

        $mockClient = $this->createMock(Client::class);
        $mockClient->expects($this->any())
            ->method('patch')
            ->willReturn(new Response(200, [], json_encode($expectedResult)));
        $nix = new AccuNixApi();
        $nix->setClient($mockClient);

        // Act
        $res = $nix->addUserInfo($userToken, json_decode($data, true));

        // Assert
        $this->assertEquals($expectedResult, $res);
        $this->assertEquals('success', $res['message']);
    }

    /**
     *
     * @dataProvider userInfoProvider
     */
    public function testAddUserInfoWith404Error(string $data)
    {
        // Arrange
        $userToken = 'USER_TOKEN';
        $expectedResult = [
            'message' => 'error: {{ErrorToken}}',
        ];

        $mockClient = $this->createMock(Client::class);
        $mockClient->expects($this->any())
            ->method('patch')
            ->willThrowException(
                new AccuNixException('Unprocessable Entity',
                    new Request('POST', 'https://api-tf.accunix.net/api/line/{botid}/users/data'),
                    new Response(404, [], json_encode($expectedResult)))
            );
        $nix = new AccuNixApi();
        $nix->setClient($mockClient);

        // Assert
        $this->expectException(AccuNixException::class);
        $this->expectExceptionMessage('Unprocessable Entity');

        // Act
        $res = $nix->addUserInfo($userToken, json_decode($data, true));
    }

    /**
     *
     * @dataProvider userInfoProvider
     */
    public function testAddUserInfoWith422Error(string $data)
    {
        // Arrange
        $userToken = 'USER_TOKEN';
        $expectedResult = [
            'message' => 'error: {{ErrorToken}}',
        ];

        $mockClient = $this->createMock(Client::class);
        $mockClient->expects($this->any())
            ->method('patch')
            ->willThrowException(
                new AccuNixException('Unprocessable Entity',
                    new Request('POST', 'https://api-tf.accunix.net/api/line/{botid}/users/data'),
                    new Response(422, [], json_encode($expectedResult)))
            );
        $nix = new AccuNixApi();
        $nix->setClient($mockClient);

        // Assert
        $this->expectException(AccuNixException::class);
        $this->expectExceptionMessage('Unprocessable Entity');

        // Act
        $nix->addUserInfo($userToken, json_decode($data, true));
    }

    public function testCreateTagSuccess()
    {
        // Arrange
        $tagName = 'TAG_NAME';
        $days = 1;
        $description = "DESCRIPTION";
        $expectedResult = [
            'message' => 'success',
        ];

        $mockClient = $this->createMock(Client::class);
        $mockClient->expects($this->any())
            ->method('post')
            ->willReturn(new Response(200, [], json_encode($expectedResult)));
        $nix = new AccuNixApi();
        $nix->setClient($mockClient);

        // Act
        $res = $nix->createTag($tagName, $days, $description);

        // Assert
        $this->assertEquals($expectedResult, $res);
        $this->assertEquals('success', $res['message']);
    }

    public function testCreateTagWith422Error()
    {
        // Arrange
        $tagName = 'TAG_NAME';
        $days = 1;
        $description = "DESCRIPTION";
        $expectedResult = [
            'message' => 'name 名稱重複',
        ];

        $mockClient = $this->createMock(Client::class);
        $mockClient->expects($this->any())
            ->method('post')
            ->willThrowException(
                new AccuNixException('Unprocessable Entity',
                    new Request('POST', 'https://api-tf.accunix.net/api/line/{botid}/tag/create'),
                    new Response(422, [], json_encode($expectedResult)))
            );
        $nix = new AccuNixApi();
        $nix->setClient($mockClient);

        // Assert
        $this->expectException(AccuNixException::class);
        $this->expectExceptionMessage('Unprocessable Entity');

        // Act
        $res = $nix->createTag($tagName, $days, $description);
    }

    public static function invalidDayProvider(): array
    {
        return [
            [0],
            [366],
            [367],
        ];
    }

    /**
     * @dataProvider invalidDayProvider
     */
    public function testCreateTagWithInvalidDayError(int $days)
    {
        // Arrange
        $tagName = 'TAG_NAME';
        $description = "DESCRIPTION";
        $expectedResult = [
            'message' => 'name 名稱重複',
        ];

        $mockClient = $this->createMock(Client::class);
        $nix = new AccuNixApi();
        $nix->setClient($mockClient);

        // Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('days must be between 1 and 365 or set -1 to be forever');

        // Act
        $res = $nix->createTag($tagName, $days, $description);
    }

    public static function tagProvider(): array
    {
        return [
            [
                [
                    'USERTOKEN',
                ],
                [
                    'TAG',
                ]
            ],
            [
                [
                    'USERTOKEN',
                    'USERTOKEN',
                ],
                [
                    'TAG',
                    'TAG',
                ]
            ],
            [
                [
                    'USERTOKEN',
                    'USERTOKEN',
                    'USERTOKEN',
                ],
                [
                    'TAG',
                    'TAG',
                    'TAG',
                ]
            ],

        ];
    }

    /**
     * @dataProvider tagProvider
     */
    public function testAddTagSuccess(array $users, array $tags)
    {
        // Arrange
        $expectedResult = [
            'message' => 'success',
        ];

        $mockClient = $this->createMock(Client::class);
        $mockClient->expects($this->any())
            ->method('post')
            ->willReturn(new Response(200, [], json_encode($expectedResult)));
        $nix = new AccuNixApi();
        $nix->setClient($mockClient);

        // Act
        $res = $nix->addTag($users, $tags);

        // Assert
        $this->assertEquals($expectedResult, $res);
        $this->assertEquals('success', $res['message']);
    }

    public static function tagInvalidTagsProvider(): array
    {
        return [
            [
                [
                    'USERTOKEN',
                ],
                [
                ]
            ],
            [
                [
                    'USERTOKEN',
                    'USERTOKEN',
                ],
                [
                    'TAG',
                    'TAG',
                    'TAG',
                    'TAG',
                ]
            ],
        ];
    }

    /**
     * @dataProvider tagInvalidTagsProvider
     */
    public function testAddTagWithInvalidTagsCount(array $users, array $tags)
    {
        // Arrange
        $expectedResult = [
            'message' => 'userTokens 必需為陣列',
        ];

        $mockClient = $this->createMock(Client::class);
        $nix = new AccuNixApi();
        $nix->setClient($mockClient);

        // Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('tags 數量錯誤');

        // Act
        $res = $nix->addTag($users, $tags);
    }


    public static function tagInvalidUsersProvider(): array
    {
        return [
            [
                [
                ],
                [
                    'TAG',
                ]
            ],
            [
                [
                    'USERTOKEN',
                    'USERTOKEN',
                    'USERTOKEN',
                    'USERTOKEN',
                    'USERTOKEN',
                    'USERTOKEN',
                    'USERTOKEN',
                    'USERTOKEN',
                    'USERTOKEN',
                    'USERTOKEN',
                    'USERTOKEN',
                ],
                [
                    'TAG',
                ]
            ],
        ];
    }

    /**
     * @dataProvider tagInvalidUsersProvider
     */
    public function testAddTagWithInvalidUsersCount(array $users, array $tags)
    {
        // Arrange
        $expectedResult = [
            'message' => 'userTokens 必需為陣列',
        ];

        $mockClient = $this->createMock(Client::class);
        $nix = new AccuNixApi();
        $nix->setClient($mockClient);

        // Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('users 數量錯誤');

        // Act
        $res = $nix->addTag($users, $tags);
    }

    /**
     * @dataProvider tagProvider
     */
    public function testRemoveTagSuccess(array $users, $tags)
    {
        // Arrange
        $expectedResult = 'success';

        $mockClient = $this->createMock(Client::class);
        $mockClient->expects($this->any())
            ->method('post')
            ->willReturn(new Response(200, [], json_encode($expectedResult)));
        $nix = new AccuNixApi();
        $nix->setClient($mockClient);

        // Act
        $res = $nix->removeTag($users, $tags);

        // Assert
        $this->assertEquals($expectedResult, $res);
    }

    /**
     * @dataProvider tagInvalidTagsProvider
     */
    public function testRemoveTagWithInvalidTagsCount(array $users, array $tags)
    {
        // Arrange
        $expectedResult = [
            'message' => 'userTokens 必需為陣列',
        ];

        $mockClient = $this->createMock(Client::class);
        $nix = new AccuNixApi();
        $nix->setClient($mockClient);

        // Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('tags 數量錯誤');

        // Act
        $res = $nix->removeTag($users, $tags);
    }


    /**
     * @dataProvider tagInvalidUsersProvider
     */
    public function testRemoveTagWithInvalidUsersCount(array $users, array $tags)
    {
        // Arrange
        $expectedResult = [
            'message' => 'userTokens 必需為陣列',
        ];

        $mockClient = $this->createMock(Client::class);
        $nix = new AccuNixApi();
        $nix->setClient($mockClient);

        // Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('users 數量錯誤');

        // Act
        $res = $nix->removeTag($users, $tags);
    }


    public function testGetReferralInfoSuccess()
    {
        // Arrange
        $id = 347;
        $expectedResult = [
            "data" => [
                "name" => "推薦好友目標",
                "end_at" => "2021-01-22 18:52:00",
                "start_at" => "2021-01-13 18:52:00",
                "is_active" => 0,
                "created_at" => "2021-01-13 18:52:32",
                "updated_at" => "2021-01-14 18:13:56",
                "description" => "說明",
                "total_share_count" => 0
            ],
            "message" => "Success"
        ];

        $mockClient = $this->createMock(Client::class);
        $mockClient->expects($this->any())
            ->method('get')
            ->willReturn(new Response(200, [], json_encode($expectedResult)));
        $nix = new AccuNixApi();
        $nix->setClient($mockClient);

        $res = $nix->getReferralInfo($id);

        // Act
        $this->assertEquals($expectedResult, $res);
        $this->assertEquals('Success', $res['message']);
    }

    /**
     * TODO on error case unitTest
     * @return void
     */
    public function testReferralShareUser()
    {
        // Arrange
        $id = 1;
        $userToken = "USERTOKEN";
        $expectedResult = [
            'message' => 'success',
            'data' => [
                'name' => 'NAME',
                'picture' => 'url',
                'share_count' => 0,
            ],
        ];

        $mockClient = $this->createMock(Client::class);
        $mockClient->expects($this->any())
            ->method('get')
            ->willReturn(new Response(200, [], json_encode($expectedResult)));
        $nix = new AccuNixApi();
        $nix->setClient($mockClient);

        // Act
        $res = $nix->referralShareUser($userToken, $id);

        // Assert
        $this->assertEquals($expectedResult, $res);
        $this->assertEquals('success', $res['message']);
    }

    /**
     * TODO on error case unitTest
     * @return void
     */
    public function testGetShareLink()
    {
        // Arrange
        $userToken = "USERTOKEN";
        $expectedResult = [
            'message' => 'ShareLink',
        ];

        $mockClient = $this->createMock(Client::class);
        $mockClient->expects($this->any())
            ->method('post')
            ->willReturn(new Response(200, [], json_encode($expectedResult)));
        $nix = new AccuNixApi();
        $nix->setClient($mockClient);

        // Act
        $res = $nix->getShareLink($userToken);

        // Assert
        $this->assertEquals($expectedResult, $res);
        $this->assertEquals('ShareLink', $res['message']);
    }

    /**
     * TODO on error case unitTest
     * @return void
     */
    public function testGetProfile()
    {
        // Arrange
        $userToken = "USERTOKEN";
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
        $expectedResult = json_decode($json, true);

        $mockClient = $this->createMock(Client::class);
        $mockClient->expects($this->any())
            ->method('get')
            ->willReturn(new Response(200, [], json_encode($expectedResult)));
        $nix = new AccuNixApi();
        $nix->setClient($mockClient);

        // Act
        $res = $nix->getProfile($userToken);

        // Assert
        $this->assertEquals($expectedResult, $res);
    }

    /**
     * TODO on error case unitTest
     * @return void
     */
    public function testAuthenticate()
    {
        // Arrange
        $roleId = 1;
        $userToken = "USERTOKEN";
        $expectedResult = [
            'message' => 'success',
        ];
        $mockClient = $this->createMock(Client::class);
        $mockClient->expects($this->any())
            ->method('post')
            ->willReturn(new Response(200, [], json_encode($expectedResult)));
        $nix = new AccuNixApi();
        $nix->setClient($mockClient);

        // Act
        $res = $nix->authenticate($userToken, $roleId);

        // Assert
        $this->assertEquals($expectedResult, $res);
        $this->assertEquals('success', $res['message']);
    }

    public function testAuthenticateRemove()
    {
        // Arrange
        $roleId = 1;
        $userToken = "USERTOKEN";
        $expectedResult = [
            'message' => 'success',
        ];
        $mockClient = $this->createMock(Client::class);
        $mockClient->expects($this->any())
            ->method('post')
            ->willReturn(new Response(200, [], json_encode($expectedResult)));
        $nix = new AccuNixApi();
        $nix->setClient($mockClient);

        // Act
        $res = $nix->authenticateRemove($userToken, $roleId);

        // Act
        $this->assertEquals($expectedResult, $res);
        $this->assertEquals('success', $res['message']);

    }
}