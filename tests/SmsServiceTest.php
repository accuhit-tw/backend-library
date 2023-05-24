<?php

namespace Accuhit\Tests;

use Accuhit\BackendLibrary\SmsService;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

final class SmsServiceTest extends TestCase
{

    public function testCreate()
    {
        // Arrange
        $smsService = new SmsService();

        // Act
        $code = $smsService->create();

        // Assert
        $this->assertIsString($code);
        $this->assertEquals(4, strlen($code));
    }
    public function testSend()
    {
        // Arrange
        $phone = "0900000000";
        $msg = "unit test";
        $platform = "backend-library";

        $expectedResult = [
            'status' => '00000',
            'message' => 'Request successfully processed.',
            'messageId' => '11418770083106',
            'data' => '',
            'sStatus' => 'Y',
        ];

        $httpClientMock = $this->createMock(Client::class);
        $httpClientMock->expects($this->once())
            ->method('get')
            ->willReturn(new Response(200, [], json_encode($expectedResult)));

        $smsService = new SmsService();
        $smsService->setClient($httpClientMock);

        // Act
        $res = $smsService->send($phone, $msg, $platform);

        // Assert
        $this->assertEquals(true, $res);

    }

}
