<?php

namespace Accuhit\Tests;

use Accuhit\BackendLibrary\Exceptions\InvoiceException;
use Accuhit\BackendLibrary\InvoiceApi;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

final class InvoiceApiTest extends TestCase
{
    public function testGetTypeList()
    {
        $typeList = InvoiceApi::getTypeList();

        $this->assertIsArray($typeList);
        $this->assertEquals('Barcode', InvoiceApi::TYPE_BARCODE);
        $this->assertEquals('QRCode', InvoiceApi::TYPE_QRCODE);
        $this->assertArrayHasKey(InvoiceApi::TYPE_BARCODE, $typeList);
        $this->assertArrayHasKey(InvoiceApi::TYPE_QRCODE, $typeList);

        $this->assertEquals('一維條碼', $typeList[InvoiceApi::TYPE_BARCODE]);
        $this->assertEquals('二維條碼', $typeList[InvoiceApi::TYPE_QRCODE]);
    }

    public function testFormatPhase()
    {
        $api = new InvoiceApi();
        $invoiceDate = "2022-01-01";
        $res = $api->formatPhase($invoiceDate);
        $this->assertEquals('11102', $res);

        $invoiceDate = "2022-01-30";
        $res = $api->formatPhase($invoiceDate);
        $this->assertEquals('11102', $res);

        $invoiceDate = "2022-02-01";
        $res = $api->formatPhase($invoiceDate);
        $this->assertEquals('11102', $res);

        $invoiceDate = "2022-11-01";
        $res = $api->formatPhase($invoiceDate);
        $this->assertEquals('11112', $res);

    }

    public function testValidNumber()
    {
        $api = new InvoiceApi();
        $invoiceNumber = "AA12345678";
        $res = $api->validNumber($invoiceNumber);
        $this->assertEquals(1, $res);

        $invoiceNumber = "AA-12345678";
        $res = $api->validNumber($invoiceNumber);
        $this->assertEquals(0, $res);
        $invoiceNumber = "AA1234";
        $res = $api->validNumber($invoiceNumber);
        $this->assertEquals(0, $res);
    }


    public function testGetInvoiceDetailsTypeFail()
    {
        $api = new InvoiceApi();
        $params = [
            'type' => "Unit Test",
            'invoiceNumber' => '',
            'invoiceDate' => '',
            'randomCode' => '',
        ];
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Not supported type: Unit Test");
        $res = $api->getInvoiceDetails(...array_values($params));
    }

    public function testGetInvoiceDetailsInvoiceNumberFail()
    {
        $api = new InvoiceApi();
        $params = [
            'type' => InvoiceApi::TYPE_BARCODE,
            'invoiceNumber' => 'AA-12345678',
            'invoiceDate' => '',
            'randomCode' => '',
        ];
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("invalid invoice number: AA-12345678");
        $res = $api->getInvoiceDetails(...array_values($params));
    }

    public function testGetInvoiceDetails()
    {
        $json = <<<JSON
{"msg":"執行成功","code":"200","invNum":"EC30150795","invoiceTime":"19:18:43",
"invStatus":"已確認","sellerName":"三商家購股份有限公司松山南京東分公司",
"invPeriod":"11110","sellerAddress":"台北市松山區南京東路四段133巷5弄8號1樓",
"sellerBan":"24456943","buyerBan":"","currency":"",
"details":[{"unitPrice":"40","amount":"40","quantity":"1","rowNum":"1","description":"銀戰士CR-2032/3V鋰電池1入"}],
"invDate":"20221011"}
JSON;
        $mock = new MockHandler([
            new Response(200, [], $json),
        ]);
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);
//        $client = new Client(); //send real request
        $api = new InvoiceApi($client);

        $params = [
            'type' => InvoiceApi::TYPE_BARCODE,
            'invoiceNumber' => 'EC30150795',
            'invoiceDate' => '2022-10-11',
            'randomCode' => '2457',
        ];
        $res = $api->getInvoiceDetails(...array_values($params));

        $this->assertArrayHasKey("msg", $res);
        $this->assertArrayHasKey("code", $res);
        $this->assertArrayHasKey("invNum", $res);
        $this->assertArrayHasKey("invoiceTime", $res);
        $this->assertArrayHasKey("invStatus", $res);
        $this->assertArrayHasKey("sellerName", $res);
        $this->assertArrayHasKey("invPeriod", $res);
        $this->assertArrayHasKey("sellerAddress", $res);
        $this->assertArrayHasKey("sellerBan", $res);
        $this->assertArrayHasKey("buyerBan", $res);
        $this->assertArrayHasKey("currency", $res);
        $this->assertArrayHasKey("details", $res);
    }

    public function testGetInvoiceDetailsTimeOut()
    {
        $params = [
            'type' => InvoiceApi::TYPE_BARCODE,
            'invoiceNumber' => 'EC30150795',
            'invoiceDate' => '2022-10-11',
            'randomCode' => '2457',
        ];
        $request = new Request('Post','https://api.einvoice.nat.gov.tw/PB2CAPIVAN/invapp/InvApp', [], json_encode($params));
        $response = new Response(408);
        $mock = new MockHandler([
            new RequestException('Time Out', $request, $response),
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);
        $api = new InvoiceApi($client);

        $this->expectException(InvoiceException::class);
        $res = $api->getInvoiceDetails(...array_values($params));
    }

    public function testGetInvoiceDetailsException()
    {
        $params = [
            'type' => InvoiceApi::TYPE_BARCODE,
            'invoiceNumber' => 'EC30150795',
            'invoiceDate' => '2022-10-11',
            'randomCode' => '2457',
        ];
        $request = new Request('Post','https://api.einvoice.nat.gov.tw/PB2CAPIVAN/invapp/InvApp', [], json_encode($params));
        $response = new Response(408);
        $mock = new MockHandler([
            new RequestException('Time Out', $request, $response),
        ]);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);
        $api = new InvoiceApi($client);

        try {
            $res = $api->getInvoiceDetails(...array_values($params));
        } catch (InvoiceException $e) {
            $data = json_decode($e->getMessage(), true);
            $this->assertArrayHasKey("message", $data);
            $this->assertArrayHasKey("statusCode", $data);
            $this->assertArrayHasKey("params", $data);
            $this->assertArrayHasKey("response", $data);
        }
    }
}
