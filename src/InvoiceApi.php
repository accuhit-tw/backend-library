<?php

namespace Accuhit\BackendLibrary;

use Accuhit\BackendLibrary\ResponseCode\InvoiceResponseCode;
use Accuhit\BackendLibrary\Exceptions\InvoiceException;
use Carbon\Carbon;
use GuzzleHttp\Client;

class InvoiceApi
{
    private string $appId;
    private string $apiKey;
    private string $url;
    private array $headers;

    const TYPE_BARCODE = 'Barcode';
    const TYPE_QRCODE = 'QRCode';

    public function __construct()
    {
        $this->appId = env("INV_APP_ID", "");
        $this->apiKey = env("INV_API_KEY", "");
        $this->url = env("INV_URL", "https://api.einvoice.nat.gov.tw/PB2CAPIVAN/invapp/InvApp");

        $this->headers = [
            'Content-Type' => 'application/x-www-form-urlencoded',
            'cache-control: no-cache'
        ];
    }

    /**
     * @return array
     */
    public static function getTypeList()
    {
        return [
            self::TYPE_BARCODE => "一維條碼",
            self::TYPE_QRCODE => "二維條碼",
        ];
    }

    /**
     * 發票期別 (發票民國年月，年分為民國年，月份必須為雙數月)
     * @param string $invoiceDate
     * @return string
     */
    public function formatPhase(string $invoiceDate): string
    {
        $phaseYear = ltrim(Carbon::parse($invoiceDate)->subYears(1911)->format('Y'), '0');
        $month = Carbon::parse($invoiceDate)->subYears(1911)->format('m');

        if ((int)$month % 2 === 1) {
            $phaseMonth = sprintf('%02d', (int)$month + 1);
        } else {
            $phaseMonth = sprintf('%02d', (int)$month);
        }

        return $phaseYear . $phaseMonth;
    }

    /**
     * @param $invoiceNumber
     * @return boolean
     */
    public function validNumber($invoiceNumber)
    {
        return preg_match('/^[A-Z]{2}\d{8}$/', $invoiceNumber);
    }

    /**
     * 取得電子發票明細
     * @param string $type
     * @param string $invoiceNumber
     * @param string $invoiceDate
     * @param string $randomCode
     * @param string $encrypt
     * @param string $sellerId
     * @throws InvoiceException
     * @throws \InvalidArgumentException
     */
    public function getInvoiceDetails(
        string $type,
        string $invoiceNumber,
        string $invoiceDate,
        string $randomCode,
        string $encrypt = '',
        string $sellerId = ''
    )
    {
        if (!in_array($type, array_keys(self::getTypeList()))) {
            throw new \InvalidArgumentException(sprintf("Not supported type: %s", $type));
        }

        if (!$this->validNumber($invoiceNumber)) {
            throw new \InvalidArgumentException(sprintf("invalid invoice number: %s", $invoiceNumber));
        }
        $invoiceDate = Carbon::parse($invoiceDate)->format('Y/m/d');

        $params = [
            'version' => '0.6',
            'type' => $type,
            'invNum' => $invoiceNumber,
            'action' => 'qryInvDetail',
            'generation' => 'V2',
            'invDate' => $invoiceDate,
            'UUID' => uniqid($type),
            'randomNumber' => $randomCode,
            'appID' => $this->appId,
        ];

        switch ($type) {
            case self::TYPE_BARCODE:
                $params['invTerm'] = $this->formatPhase($invoiceDate);
                break;
            case self::TYPE_QRCODE:
                $params['encrypt'] = $encrypt;
                $params['sellerID'] = $sellerId;
                break;
            default:
                break;
        }

        $client = new Client();
        $response = $client->post($this->url, [
            'headers' => $this->headers,
            'form_params' => $params,
        ]);

        if ($response->getStatusCode() !== 200) {
            $msg = '';

            $data = [
                'message' => $msg,
                'params' => $params,
                'response' => $response->getBody()->getContents(),
            ];
            throw new InvoiceException(json_encode($data));
        }

        $result = json_decode($response->getBody()->getContents(), true);
        if (!isset($result['code']) || $result['code'] != InvoiceResponseCode::SUCCESS) {
            $errorCode = $result['code'] ?? InvoiceResponseCode::UNKNOWN_ERROR;
            $errors = InvoiceResponseCode::getResponseCode();
            $msg = $errors[$errorCode];
            $data = [
                'message' => $msg,
                'params' => $params,
                'response' => $result,
            ];
            throw new InvoiceException(json_encode($data));
        }

        return $result;
    }
}
