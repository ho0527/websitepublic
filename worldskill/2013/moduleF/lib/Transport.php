<?php
/**
 * SOAP 傳輸層。
 *
 * 提供兩種實作：
 *  - HttpTransport     以 cURL 對中央伺服器發出真正的 HTTP POST
 *  - LoopbackTransport 直接在同一個行程內呼叫服務端點
 *
 * 需要 LoopbackTransport 的原因：本機 nginx 只掛了「單一個」php-cgi 行程，
 * 一個 PHP 請求若再對同一台伺服器發出 HTTP 請求，會因為沒有空閒的工作行程而
 * 互相等待到逾時。因此預設會先偵測是否有獨立的服務埠（見 service/serve.bat），
 * 有的話走 HTTP，沒有的話改用同行程呼叫；兩者送出與解析的 SOAP 信封完全相同。
 */

interface Transport
{
    /**
     * 送出 SOAP 請求信封並取回回應信封。
     *
     * @param string $envelope   SOAP 請求
     * @param string $soapAction SOAPAction 標頭值
     * @return string SOAP 回應
     * @throws SoapTransportException
     */
    public function send($envelope, $soapAction);

    /** 供畫面顯示的來源說明 */
    public function describe();
}

/**
 * 真正的 HTTP SOAP 傳輸。
 */
class HttpTransport implements Transport
{
    /** @var string */
    private $endpoint;

    /** @var int */
    private $timeout;

    public function __construct($endpoint, $timeout)
    {
        $this->endpoint = $endpoint;
        $this->timeout  = (int)$timeout;
    }

    public function send($envelope, $soapAction)
    {
        $handle = curl_init($this->endpoint);
        curl_setopt_array($handle, array(
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $envelope,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => $this->timeout,
            CURLOPT_TIMEOUT        => $this->timeout,
            CURLOPT_HTTPHEADER     => array(
                'Content-Type: text/xml; charset=utf-8',
                'SOAPAction: "' . $soapAction . '"',
                'Content-Length: ' . strlen($envelope),
            ),
        ));

        $response = curl_exec($handle);
        $error    = curl_error($handle);
        $status   = (int)curl_getinfo($handle, CURLINFO_HTTP_CODE);
        curl_close($handle);

        if ($response === false) {
            throw new SoapTransportException('Cannot reach the web service: ' . $error);
        }
        // SOAP Fault 以 HTTP 500 回傳，內容仍需交給呼叫端解析
        if ($status !== 200 && $status !== 500) {
            throw new SoapTransportException('The web service answered with HTTP status ' . $status . '.');
        }

        return $response;
    }

    public function describe()
    {
        return 'SOAP web service over HTTP (' . $this->endpoint . ')';
    }
}

/**
 * 同行程的 SOAP 傳輸：把請求信封交給中央服務的端點類別處理。
 */
class LoopbackTransport implements Transport
{
    /** @var string 服務程式所在目錄 */
    private $servicePath;

    public function __construct($servicePath)
    {
        $this->servicePath = rtrim($servicePath, '/\\');
    }

    public function send($envelope, $soapAction)
    {
        $endpointFile = $this->servicePath . '/SoapEndpoint.php';
        if (!is_file($endpointFile)) {
            throw new SoapTransportException('The central web service is not installed at ' . $this->servicePath);
        }

        require_once $endpointFile;
        $endpoint = new SoapEndpoint($this->servicePath . '/data');
        $result   = $endpoint->handle($envelope);

        return $result['body'];
    }

    public function describe()
    {
        return 'SOAP web service, in-process call (' . str_replace('\\', '/', $this->servicePath) . ')';
    }
}
