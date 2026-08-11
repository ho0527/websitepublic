<?php
/**
 * 手寫的極簡 SOAP 1.1 用戶端。
 *
 * 題目要求本模組不得使用伺服端函式庫，程式必須自行撰寫；
 * 本機的 PHP 也沒有安裝 ext-soap，因此這裡自行組出 rpc/encoded 的
 * SOAP 信封，交給 Transport 送出，再用 DOM 解析回應。
 */
class SoapClientLite
{
    /** @var Transport */
    private $transport;

    /** @var string 操作所屬的命名空間（見 WSDL 的 soap:body namespace） */
    private $namespace;

    public function __construct(Transport $transport, $namespace)
    {
        $this->transport = $transport;
        $this->namespace = $namespace;
    }

    /**
     * 呼叫遠端操作。
     *
     * @param string $operation 操作名稱，例如 getList
     * @param array  $arguments 參數，例如 array('listType' => 'countrys')
     * @return DOMXPath 已註冊命名空間、指向回應信封的 XPath 物件
     * @throws SoapTransportException  連線或傳輸層失敗
     * @throws SoapFaultException      伺服器回傳 SOAP Fault
     */
    public function call($operation, array $arguments = array())
    {
        $envelope = $this->buildEnvelope($operation, $arguments);
        $response = $this->transport->send($envelope, $this->namespace . '#' . $operation);

        $document = new DOMDocument();
        libxml_use_internal_errors(true);
        $loaded = $document->loadXML($response);
        libxml_clear_errors();

        if (!$loaded) {
            throw new SoapTransportException(
                'The web service returned a response that is not well-formed XML.');
        }

        $xpath = new DOMXPath($document);
        $xpath->registerNamespace('env', 'http://schemas.xmlsoap.org/soap/envelope/');
        $xpath->registerNamespace('ns1', $this->namespace);

        // 伺服器回傳 Fault 時轉為例外
        $faults = $xpath->query('//env:Fault');
        if ($faults->length > 0) {
            $message = $xpath->query('.//faultstring', $faults->item(0));
            throw new SoapFaultException($message->length > 0
                ? trim($message->item(0)->textContent)
                : 'The web service returned a SOAP fault.');
        }

        return $xpath;
    }

    /** 目前使用的傳輸方式說明 */
    public function describeTransport()
    {
        return $this->transport->describe();
    }

    /**
     * 組出 SOAP 1.1 (rpc/encoded) 請求信封。
     */
    private function buildEnvelope($operation, array $arguments)
    {
        $body = '';
        foreach ($arguments as $name => $value) {
            $body .= "\n\t\t\t<" . $name . ' xsi:type="xsd:string">'
                   . htmlspecialchars((string)$value, ENT_XML1 | ENT_COMPAT, 'UTF-8')
                   . '</' . $name . '>';
        }

        return '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
            . '<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"'
            . ' xmlns:ns1="' . $this->namespace . '"'
            . ' xmlns:xsd="http://www.w3.org/2001/XMLSchema"'
            . ' xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"'
            . ' xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/"'
            . ' SOAP-ENV:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">' . "\n"
            . "\t<SOAP-ENV:Body>\n"
            . "\t\t<ns1:" . $operation . '>' . $body . "\n"
            . "\t\t</ns1:" . $operation . ">\n"
            . "\t</SOAP-ENV:Body>\n"
            . '</SOAP-ENV:Envelope>';
    }
}

/** 連線／傳輸層錯誤 */
class SoapTransportException extends RuntimeException
{
}

/** 伺服器回傳的 SOAP Fault */
class SoapFaultException extends RuntimeException
{
}
