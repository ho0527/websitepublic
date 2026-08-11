<?php
/**
 * 中央統計 Web Service 的核心：接收 SOAP 1.1 請求信封，回傳 SOAP 回應信封。
 *
 * 題目原始檔以 PHP 的 ext-soap（SoapServer）實作，但本機 PHP 8.3 未安裝該擴充，
 * 因此改為手寫的 SOAP 1.1 (rpc/encoded) 端點，回應格式與題目提供的
 * WSC-CountrysSOAP.xml 範例、以及 WSC_Statistics.wsdl 完全一致。
 *
 * 本類別與傳輸方式無關：WSC_Statistics.php 以 HTTP 呼叫它，
 * 前端在無法使用 HTTP 時也可以直接呼叫（見 lib/LoopbackTransport.php）。
 */
require_once __DIR__ . '/StatisticsDataSource.php';

class SoapEndpoint
{
    /** @var StatisticsDataSource */
    private $source;

    public function __construct($dataDirectory = null)
    {
        $this->source = new StatisticsDataSource($dataDirectory === null ? __DIR__ . '/data' : $dataDirectory);
    }

    /**
     * 處理一個 SOAP 請求。
     *
     * @param string $requestXml SOAP 請求信封
     * @return array array('status' => int, 'body' => string)
     */
    public function handle($requestXml)
    {
        if (trim((string)$requestXml) === '') {
            return $this->fault('Client',
                'Expected a SOAP 1.1 request. Append ?wsdl to retrieve the service description.');
        }

        $document = new DOMDocument();
        libxml_use_internal_errors(true);
        $loaded = $document->loadXML($requestXml);
        libxml_clear_errors();
        if (!$loaded) {
            return $this->fault('Client', 'The SOAP request is not well-formed XML.');
        }

        $xpath = new DOMXPath($document);
        $xpath->registerNamespace('env', 'http://schemas.xmlsoap.org/soap/envelope/');
        $bodyNodes = $xpath->query('/env:Envelope/env:Body/*');
        if ($bodyNodes->length === 0) {
            return $this->fault('Client', 'The SOAP body does not contain an operation.');
        }

        $operationNode = $bodyNodes->item(0);

        try {
            switch ($operationNode->localName) {

                case 'getList':
                    $listType = $this->argument($operationNode, 'listType');
                    if ($listType === 'countrys') {
                        return $this->response('getListResponse', $this->renderCountrys($this->source->countrys()));
                    }
                    if ($listType === 'skills') {
                        return $this->response('getListResponse', $this->renderSkills($this->source->skills()));
                    }
                    return $this->fault('Client',
                        'Unknown listType "' . $listType . '". Expected "countrys" or "skills".');

                case 'getResults':
                    return $this->response('getResultsResponse', $this->renderResults($this->source->results()));

                default:
                    return $this->fault('Client', 'Unknown operation "' . $operationNode->localName . '".');
            }
        } catch (RuntimeException $exception) {
            return $this->fault('Server', $exception->getMessage());
        }
    }

    /**
     * 取得服務描述檔，並把 soap:address 換成實際的端點位址。
     *
     * @param string $endpointUrl 目前服務的實際網址
     * @return string
     */
    public function wsdl($endpointUrl)
    {
        $wsdl = file_get_contents(__DIR__ . '/WSC_Statistics.wsdl');
        return preg_replace("#<soap:address location='[^']*'/>#",
                            "<soap:address location='" . htmlspecialchars($endpointUrl, ENT_QUOTES) . "'/>",
                            $wsdl);
    }

    // ------------------------------------------------------------------
    // 內部實作
    // ------------------------------------------------------------------

    /** 由操作節點取出指定參數的值 */
    private function argument(DOMNode $operationNode, $name)
    {
        foreach ($operationNode->childNodes as $child) {
            if ($child->nodeType === XML_ELEMENT_NODE && $child->localName === $name) {
                return trim($child->textContent);
            }
        }
        return '';
    }

    /** 國家清單（iso 為屬性，國名為文字內容，與題目範例一致） */
    private function renderCountrys(array $countrys)
    {
        $xml = "\t\t\t\t<countrys xsi:type=\"ns2:countrys\">\n";
        foreach ($countrys as $country) {
            $xml .= "\t\t\t\t\t<country xsi:type=\"ns2:country\" iso=\"" . self::attr($country['iso']) . "\">"
                  . self::text($country['name']) . "</country>\n";
        }
        return $xml . "\t\t\t\t</countrys>\n";
    }

    /** 職類清單（number 為屬性，name_en 為子元素） */
    private function renderSkills(array $skills)
    {
        $xml = "\t\t\t\t<skills xsi:type=\"ns2:skills\">\n";
        foreach ($skills as $skill) {
            $xml .= "\t\t\t\t\t<skill xsi:type=\"ns2:skill\" number=\"" . self::attr($skill['number']) . "\">\n"
                  . "\t\t\t\t\t\t<name_en xsi:type=\"xsd:string\">" . self::text($skill['name']) . "</name_en>\n"
                  . "\t\t\t\t\t</skill>\n";
        }
        return $xml . "\t\t\t\t</skills>\n";
    }

    /** 競賽成績清單 */
    private function renderResults(array $results)
    {
        $xml = "\t\t\t\t<results xsi:type=\"ns2:results\">\n";
        foreach ($results as $result) {
            $xml .= "\t\t\t\t\t<result xsi:type=\"ns2:result\">\n"
                  . "\t\t\t\t\t\t<year xsi:type=\"xsd:int\">" . self::text($result['year']) . "</year>\n"
                  . "\t\t\t\t\t\t<skill_number xsi:type=\"xsd:string\">" . self::text($result['skill_number']) . "</skill_number>\n"
                  . "\t\t\t\t\t\t<country_iso xsi:type=\"xsd:string\">" . self::text($result['country_iso']) . "</country_iso>\n"
                  . "\t\t\t\t\t\t<score xsi:type=\"ns2:score\">\n"
                  . "\t\t\t\t\t\t\t<award xsi:type=\"xsd:string\">" . self::text($result['award']) . "</award>\n"
                  . "\t\t\t\t\t\t\t<points xsi:type=\"xsd:int\">" . self::text($result['points']) . "</points>\n"
                  . "\t\t\t\t\t\t</score>\n"
                  . "\t\t\t\t\t</result>\n";
        }
        return $xml . "\t\t\t\t</results>\n";
    }

    /** 組出成功的 SOAP 回應信封 */
    private function response($responseName, $listXml)
    {
        $body = '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
            . '<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"'
            . ' xmlns:ns1="urn:WSCstats"'
            . ' xmlns:xsd="http://www.w3.org/2001/XMLSchema"'
            . ' xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"'
            . ' xmlns:ns2="http://www.mytest.com/WSCstats"'
            . ' xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/"'
            . ' SOAP-ENV:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">' . "\n"
            . "\t<SOAP-ENV:Body>\n"
            . "\t\t<ns1:" . $responseName . ">\n"
            . "\t\t\t<list xsi:type=\"ns2:list\">\n"
            . $listXml
            . "\t\t\t</list>\n"
            . "\t\t</ns1:" . $responseName . ">\n"
            . "\t</SOAP-ENV:Body>\n"
            . '</SOAP-ENV:Envelope>';

        return array('status' => 200, 'body' => $body);
    }

    /** 組出 SOAP Fault 信封 */
    private function fault($code, $message)
    {
        $body = '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
            . '<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/">' . "\n"
            . "\t<SOAP-ENV:Body>\n\t\t<SOAP-ENV:Fault>\n"
            . "\t\t\t<faultcode>SOAP-ENV:" . self::text($code) . "</faultcode>\n"
            . "\t\t\t<faultstring>" . self::text($message) . "</faultstring>\n"
            . "\t\t</SOAP-ENV:Fault>\n\t</SOAP-ENV:Body>\n"
            . '</SOAP-ENV:Envelope>';

        return array('status' => 500, 'body' => $body);
    }

    /** 文字節點跳脫 */
    private static function text($value)
    {
        return htmlspecialchars((string)$value, ENT_XML1 | ENT_COMPAT, 'UTF-8');
    }

    /** 屬性值跳脫 */
    private static function attr($value)
    {
        return htmlspecialchars((string)$value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
