<?php
/**
 * Module F 全域設定。
 */
class Config
{
    /**
     * 中央 Web Service 在本站（nginx）上的位址。
     * 由瀏覽器直接開啟時可用；但 PHP 程式本身不要對它發 HTTP 請求，
     * 原因見 lib/Transport.php 的說明（本機只有單一個 php-cgi 工作行程）。
     */
    const SERVICE_URL = 'http://127.0.0.1:83/worldskill/2013/moduleF/service/WSC_Statistics.php';

    /**
     * 獨立執行的中央服務位址（由 service/serve.bat 啟動）。
     * 只要這個埠有在監聽，前端就會改用真正的 HTTP SOAP 呼叫。
     */
    const STANDALONE_HOST = '127.0.0.1';
    const STANDALONE_PORT = 8017;
    const STANDALONE_ENDPOINT = 'http://127.0.0.1:8017/WSC_Statistics.php';

    /** SOAP 服務的命名空間（見 WSDL 的 soap:body namespace） */
    const SOAP_NAMESPACE = 'urn:WSCstats';

    /** 中央服務程式所在目錄 */
    public static function servicePath()
    {
        return dirname(__DIR__) . '/service';
    }

    /** 連線逾時秒數 */
    const TIMEOUT = 15;

    /** 偵測獨立服務是否在監聽時使用的逾時秒數 */
    const PROBE_TIMEOUT = 0.2;

    /** 圖表中要呈現的競賽年度 */
    public static function years()
    {
        return array(2007, 2009, 2011);
    }

    /** 獎項的顯示名稱（原始資料大小寫不一致，統一在此對應） */
    public static function awardLabels()
    {
        return array(
            'GOLD'                     => 'Gold',
            'SILVER'                   => 'Silver',
            'BRONZE'                   => 'Bronze',
            'MEDALLION FOR EXCELLENCE' => 'Medallion for Excellence',
        );
    }

    /**
     * 建立適用的 SOAP 傳輸方式。
     * 有獨立服務在監聽時走 HTTP，否則使用同行程呼叫。
     */
    public static function createTransport()
    {
        $socket = @fsockopen(self::STANDALONE_HOST, self::STANDALONE_PORT, $errno, $errstr, self::PROBE_TIMEOUT);
        if ($socket !== false) {
            fclose($socket);
            return new HttpTransport(self::STANDALONE_ENDPOINT, self::TIMEOUT);
        }
        return new LoopbackTransport(self::servicePath());
    }
}
