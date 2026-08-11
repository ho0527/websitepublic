<?php
/**
 * 中央伺服器端的資料來源：讀取 data/ 目錄下的 XML 原始檔。
 *
 * 這些檔案就是「中央伺服器的資料」，評分時只要修改它們，
 * 前端透過 SOAP 取得的內容就會立即跟著改變。
 */
class StatisticsDataSource
{
    /** @var string XML 檔案所在目錄 */
    private $directory;

    public function __construct($directory)
    {
        $this->directory = rtrim($directory, '/\\');
    }

    /**
     * 國家清單。
     *
     * @return array[] 每筆為 array('iso' => ..., 'name' => ...)
     */
    public function countrys()
    {
        $items = array();
        foreach ($this->query('WSC-Countrys.xml', '//country') as $node) {
            $items[] = array(
                'iso'  => $this->childValue($node, 'iso'),
                'name' => $this->childValue($node, 'name-en'),
            );
        }
        return $items;
    }

    /**
     * 職類清單。
     *
     * @return array[] 每筆為 array('number' => ..., 'name' => ...)
     */
    public function skills()
    {
        $items = array();
        foreach ($this->query('WSC-Skills.xml', '//skill') as $node) {
            $items[] = array(
                'number' => $this->childValue($node, 'number'),
                'name'   => $this->childValue($node, 'name-en'),
            );
        }
        return $items;
    }

    /**
     * 競賽成績。
     *
     * @return array[] 每筆為 array('year','skill_number','country_iso','award','points')
     */
    public function results()
    {
        $items = array();
        foreach ($this->query('WSC-Results.xml', '//result') as $node) {
            $items[] = array(
                'year'         => $this->childValue($node, 'year'),
                'skill_number' => $this->childValue($node, 'skill-number'),
                'country_iso'  => $this->childValue($node, 'country-iso'),
                'award'        => $this->childValue($node, 'medal'),
                'points'       => $this->childValue($node, 'score'),
            );
        }
        return $items;
    }

    /**
     * 載入 XML 檔並執行 XPath 查詢。
     *
     * @return DOMNodeList
     */
    private function query($fileName, $expression)
    {
        $path = $this->directory . DIRECTORY_SEPARATOR . $fileName;
        if (!is_file($path)) {
            throw new RuntimeException('Data file not found: ' . $fileName);
        }

        $document = new DOMDocument();
        libxml_use_internal_errors(true);
        $loaded = $document->loadXML(file_get_contents($path));
        libxml_clear_errors();
        if (!$loaded) {
            throw new RuntimeException('Data file is not well-formed XML: ' . $fileName);
        }

        $xpath = new DOMXPath($document);
        return $xpath->query($expression);
    }

    /**
     * 取得子元素的文字內容（不存在時回傳空字串）。
     */
    private function childValue(DOMNode $node, $name)
    {
        foreach ($node->childNodes as $child) {
            if ($child->nodeType === XML_ELEMENT_NODE && $child->nodeName === $name) {
                return trim($child->textContent);
            }
        }
        return '';
    }
}
