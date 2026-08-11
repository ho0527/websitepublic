<?php
/**
 * 資料閘道：負責向中央伺服器取得國家、職類與成績資料。
 *
 * 優先使用 SOAP Web Service；若服務無法使用，改讀中央伺服器上的純 XML 檔
 * （即 SOAP body 的原始資料來源）。兩種來源都是每次請求即時讀取，
 * 因此中央資料的異動會立刻反映在畫面上。
 */
class StatisticsGateway
{
    /** @var string 實際使用的來源說明，供畫面顯示 */
    private $sourceLabel = '';

    /** @var string[] 取得資料過程中出現的警告訊息 */
    private $warnings = array();

    /** @var array 單次請求內的記憶體快取，避免重複呼叫 */
    private $cache = array();

    /**
     * 國家清單，依 ISO 代碼排序。
     *
     * @return array[] array('iso' => ..., 'name' => ...)
     */
    public function countrys()
    {
        if (isset($this->cache['countrys'])) {
            return $this->cache['countrys'];
        }

        $items = $this->fetch(
            // 由 SOAP 回應取出
            function (DOMXPath $xpath) {
                $result = array();
                foreach ($xpath->query('//countrys/country') as $node) {
                    $result[] = array(
                        'iso'  => trim($node->getAttribute('iso')),
                        'name' => trim($node->textContent),
                    );
                }
                return $result;
            },
            'getList', array('listType' => 'countrys'),
            // 由備援 XML 取出
            'WSC-Countrys.xml',
            function (DOMXPath $xpath) {
                $result = array();
                foreach ($xpath->query('//country') as $node) {
                    $result[] = array(
                        'iso'  => self::childText($node, 'iso'),
                        'name' => self::childText($node, 'name-en'),
                    );
                }
                return $result;
            }
        );

        usort($items, function ($a, $b) {
            return strcmp($a['iso'], $b['iso']);
        });

        $this->cache['countrys'] = $items;
        return $items;
    }

    /**
     * 職類清單，依職類編號排序（數字在前、字母代碼在後）。
     *
     * @return array[] array('number' => ..., 'name' => ...)
     */
    public function skills()
    {
        if (isset($this->cache['skills'])) {
            return $this->cache['skills'];
        }

        $items = $this->fetch(
            function (DOMXPath $xpath) {
                $result = array();
                foreach ($xpath->query('//skills/skill') as $node) {
                    $result[] = array(
                        'number' => trim($node->getAttribute('number')),
                        'name'   => self::childText($node, 'name_en'),
                    );
                }
                return $result;
            },
            'getList', array('listType' => 'skills'),
            'WSC-Skills.xml',
            function (DOMXPath $xpath) {
                $result = array();
                foreach ($xpath->query('//skill') as $node) {
                    $result[] = array(
                        'number' => self::childText($node, 'number'),
                        'name'   => self::childText($node, 'name-en'),
                    );
                }
                return $result;
            }
        );

        // 原始資料中同一個編號可能重複出現，只保留第一筆
        $unique = array();
        foreach ($items as $item) {
            if ($item['number'] !== '' && !isset($unique[$item['number']])) {
                $unique[$item['number']] = $item;
            }
        }
        $items = array_values($unique);
        usort($items, array(__CLASS__, 'compareSkillNumber'));

        $this->cache['skills'] = $items;
        return $items;
    }

    /**
     * 競賽成績。
     *
     * @return array[] array('year','skill_number','country_iso','award','points')
     */
    public function results()
    {
        if (isset($this->cache['results'])) {
            return $this->cache['results'];
        }

        $items = $this->fetch(
            function (DOMXPath $xpath) {
                $result = array();
                foreach ($xpath->query('//results/result') as $node) {
                    $result[] = array(
                        'year'         => self::childText($node, 'year'),
                        'skill_number' => self::childText($node, 'skill_number'),
                        'country_iso'  => self::childText($node, 'country_iso'),
                        'award'        => self::grandChildText($node, 'score', 'award'),
                        'points'       => self::grandChildText($node, 'score', 'points'),
                    );
                }
                return $result;
            },
            'getResults', array(),
            'WSC-Results.xml',
            function (DOMXPath $xpath) {
                $result = array();
                foreach ($xpath->query('//result') as $node) {
                    $result[] = array(
                        'year'         => self::childText($node, 'year'),
                        'skill_number' => self::childText($node, 'skill-number'),
                        'country_iso'  => self::childText($node, 'country-iso'),
                        'award'        => self::childText($node, 'medal'),
                        'points'       => self::childText($node, 'score'),
                    );
                }
                return $result;
            }
        );

        $this->cache['results'] = $items;
        return $items;
    }

    /**
     * 目前實際使用的資料來源說明。
     */
    public function getSourceLabel()
    {
        return $this->sourceLabel;
    }

    /**
     * 取得過程中的警告訊息（例如 SOAP 失效而改用 XML）。
     *
     * @return string[]
     */
    public function getWarnings()
    {
        return array_values(array_unique($this->warnings));
    }

    // ------------------------------------------------------------------
    // 內部實作
    // ------------------------------------------------------------------

    /**
     * 先試 SOAP，失敗時退回純 XML。
     *
     * @param callable $parseSoap  解析 SOAP 回應的函式
     * @param string   $operation  SOAP 操作名稱
     * @param array    $arguments  SOAP 參數
     * @param string   $xmlFile    備援 XML 檔名
     * @param callable $parseXml   解析備援 XML 的函式
     * @return array
     */
    private function fetch(callable $parseSoap, $operation, array $arguments, $xmlFile, callable $parseXml)
    {
        try {
            $client = new SoapClientLite(Config::createTransport(), Config::SOAP_NAMESPACE);
            $xpath  = $client->call($operation, $arguments);
            $this->sourceLabel = $client->describeTransport();
            return $parseSoap($xpath);
        } catch (RuntimeException $exception) {
            $this->warnings[] = 'The SOAP web service is unavailable (' . $exception->getMessage()
                              . '); the plain XML files on the central server are used instead.';
        }

        // 備援：直接讀中央伺服器上的純 XML 檔（SOAP body 的原始資料來源）
        $path  = Config::servicePath() . '/data/' . $xmlFile;
        $xpath = $this->loadXmlFile($path);
        $this->sourceLabel = 'XML files on the central server (service/data/' . $xmlFile . ')';
        return $parseXml($xpath);
    }

    /**
     * 讀取 XML 檔並建立 XPath。
     *
     * @throws RuntimeException
     */
    private function loadXmlFile($path)
    {
        if (!is_file($path)) {
            throw new RuntimeException('The XML data source ' . basename($path) . ' does not exist.');
        }

        $body = file_get_contents($path);
        if ($body === false) {
            throw new RuntimeException('The XML data source ' . basename($path) . ' cannot be read.');
        }

        $document = new DOMDocument();
        libxml_use_internal_errors(true);
        $loaded = $document->loadXML($body);
        libxml_clear_errors();
        if (!$loaded) {
            throw new RuntimeException('The XML data source ' . basename($path) . ' is not well-formed.');
        }

        return new DOMXPath($document);
    }

    /**
     * 取得子元素文字內容。
     */
    private static function childText(DOMNode $node, $name)
    {
        foreach ($node->childNodes as $child) {
            if ($child->nodeType === XML_ELEMENT_NODE && $child->localName === $name) {
                return trim($child->textContent);
            }
        }
        return '';
    }

    /**
     * 取得孫元素文字內容。
     */
    private static function grandChildText(DOMNode $node, $childName, $grandChildName)
    {
        foreach ($node->childNodes as $child) {
            if ($child->nodeType === XML_ELEMENT_NODE && $child->localName === $childName) {
                return self::childText($child, $grandChildName);
            }
        }
        return '';
    }

    /**
     * 職類編號排序：純數字依數值大小，其餘（D1、HM1…）排在後面。
     */
    private static function compareSkillNumber($a, $b)
    {
        $aNumber  = (string)$a['number'];
        $bNumber  = (string)$b['number'];
        $aNumeric = ctype_digit($aNumber);
        $bNumeric = ctype_digit($bNumber);

        if ($aNumeric && $bNumeric) {
            return (int)$aNumber - (int)$bNumber;
        }
        if ($aNumeric !== $bNumeric) {
            return $aNumeric ? -1 : 1;
        }
        return strcmp($aNumber, $bNumber);
    }
}
