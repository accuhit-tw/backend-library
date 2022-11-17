<?php

namespace Accuhit\BackendLibrary\ResponseCode;

class InvoiceResponseCode
{
    const SUCCESS = 200;
    const SYSTEM_ERROR = 500;
    const CREATED_JSON_FAIL = 900;
    const DATA_NOT_FOUND = 901;
    const DATE_INVALID = 902;
    const PARAMS_INVALID = 903;
    const TYPE_ERROR = 904;
    const INVOICE_NOT_FOUND = 915;
    const PARAMS_SIGN_ERROR = 916;
    const SELECT_LIMIT = 950;
    const TIMEOUT = 951;
    const CARD_CODE_EXPIRED = 952;
    const CARD_SIGN_EXPIRED = 953;
    const SIGN_ERROR = 954;
    const RANGE_OVER_LIMIT = 996;
    const UUID_INVALID = 997;
    const APPID_INVALID = 998;
    const UNKNOWN_ERROR = 999;

    public static function getResponseCode()
    {
        return [
            self::SUCCESS => '執行成功',
            self::SYSTEM_ERROR => '系統執行錯誤',
            self::CREATED_JSON_FAIL => '建立 JSON 物件失敗',
            self::DATA_NOT_FOUND => '無此期別資料',
            self::DATE_INVALID => '期別錯誤',
            self::PARAMS_INVALID => '參數錯誤',
            self::TYPE_ERROR => '錯誤的查詢種類',
//            907 => '捐贈失敗，捐贈碼不存在',
//            908 => '捐贈失敗，此發票已被捐贈',
//            913 => '捐贈失敗，此發票開立予營業人或機關團體，不能捐贈',
            self::INVOICE_NOT_FOUND => '查無此發票詳細資料',
            self::PARAMS_SIGN_ERROR => '參數驗證碼錯誤',
            self::SELECT_LIMIT => '超過最大查詢次數',
            self::TIMEOUT => '連線逾時',
            self::CARD_CODE_EXPIRED => '卡片(QR 碼)有效存續時間已過（過期憑證）',
            self::CARD_SIGN_EXPIRED => '卡片檢查碼有誤（偽造卡片）',
            self::SIGN_ERROR => '簽名有誤（偽造之訊息、傳遞不完整）',
            self::RANGE_OVER_LIMIT => '查詢發票筆數超過上限，請縮小查詢日期區間',
            self::UUID_INVALID => 'UUID 不符合規定（黑名單）',
            self::APPID_INVALID => 'AppID 不符合規定（可能是被停權或是從未申請該 AppID）',
            self::UNKNOWN_ERROR => '未知錯誤（以避免程式當機）',
        ];
    }

}

