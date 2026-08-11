<?php
/**
 * 依 lvb_system.xsd 由資料庫內容產生 XML。
 *
 * XSD 規定的 line 子元素順序：
 *   code, type, start_time_operation, end_time_operation, count_vehicles, map,
 *   start_station?, end_station?, intermediate_stations{0,5}, vehicles_line{0,10}
 */
class LvbXmlBuilder
{
    /** @var DOMDocument */
    private $document;

    public function __construct()
    {
        $this->document = new DOMDocument('1.0', 'UTF-8');
        $this->document->formatOutput = true;
    }

    /**
     * 由資料庫產生完整的 lvb_system 文件。
     *
     * @return string XML 內容
     */
    public function build()
    {
        $root = $this->document->createElement('lvb_system');
        $this->document->appendChild($root);

        $lines = Line::model()->with('stations', 'vehicles')->findAll(array('order' => 't.code'));
        foreach ($lines as $line) {
            $root->appendChild($this->buildLine($line));
        }

        return $this->document->saveXML();
    }

    /**
     * 建立單一 line 節點。
     */
    private function buildLine(Line $line)
    {
        $node = $this->document->createElement('line');
        $node->setAttribute('id', (string)$line->id);

        $this->appendText($node, 'code', $line->code);
        $this->appendText($node, 'type', $line->type);
        $this->appendText($node, 'start_time_operation', $this->formatTime($line->start_time_operation));
        $this->appendText($node, 'end_time_operation', $this->formatTime($line->end_time_operation));
        $this->appendText($node, 'count_vehicles', (string)count($line->vehicles));
        $this->appendText($node, 'map', $line->map);

        // 起站
        $start = $line->getStationAt(Station::POS_START);
        if ($start !== null) {
            $node->appendChild($this->buildStation('start_station', $start));
        }
        // 終站
        $end = $line->getStationAt(Station::POS_END);
        if ($end !== null) {
            $node->appendChild($this->buildStation('end_station', $end));
        }
        // 中間站（XSD 允許最多 5 個）
        $intermediate = $line->getIntermediateStations();
        $intermediate = array_slice($intermediate, 0, 5);
        foreach ($intermediate as $station) {
            $node->appendChild($this->buildStation('intermediate_stations', $station));
        }

        // 車輛（XSD 允許最多 10 台）
        $vehicles = array_slice($line->vehicles, 0, 10);
        foreach ($vehicles as $vehicle) {
            $node->appendChild($this->buildVehicle($vehicle));
        }

        return $node;
    }

    /**
     * 站點節點（型別 station：name + id 屬性）。
     */
    private function buildStation($elementName, Station $station)
    {
        $node = $this->document->createElement($elementName);
        $node->setAttribute('id', (string)$station->id);
        $this->appendText($node, 'name', $station->name);
        return $node;
    }

    /**
     * 車輛節點（name, capacity, driver*）。
     */
    private function buildVehicle(Vehicle $vehicle)
    {
        $node = $this->document->createElement('vehicles_line');
        $node->setAttribute('id', (string)$vehicle->id);
        $this->appendText($node, 'name', $vehicle->name);
        $this->appendText($node, 'capacity', (string)(int)$vehicle->capacity);

        foreach ($vehicle->drivers as $driver) {
            $node->appendChild($this->buildDriver($driver));
        }
        return $node;
    }

    /**
     * 司機節點（name, birth_date, email, phone, avatar）。
     */
    private function buildDriver(Driver $driver)
    {
        $node = $this->document->createElement('driver');
        $node->setAttribute('id', (string)$driver->id);
        $this->appendText($node, 'name', $driver->name);
        $this->appendText($node, 'birth_date', $driver->birth_date);
        $this->appendText($node, 'email', $driver->email);
        $this->appendText($node, 'phone', $driver->phone);
        $this->appendText($node, 'avatar', $driver->avatar === '' ? 'avatar.png' : $driver->avatar);
        return $node;
    }

    /**
     * 建立文字元素（內容自動跳脫）。
     */
    private function appendText(DOMElement $parent, $name, $value)
    {
        $element = $this->document->createElement($name);
        $element->appendChild($this->document->createTextNode((string)$value));
        $parent->appendChild($element);
        return $element;
    }

    /**
     * 補足秒數，使其符合 xs:time 格式。
     */
    private function formatTime($time)
    {
        $time = trim((string)$time);
        if (preg_match('/^\d{2}:\d{2}$/', $time)) {
            return $time . ':00';
        }
        return $time;
    }

    /**
     * 以題目提供的 lvb_system.xsd 驗證 XML。
     *
     * @param string $xml
     * @return array array('valid' => bool, 'errors' => string[])
     */
    public static function validate($xml)
    {
        $xsdFile = Yii::app()->params['xsdFile'];
        if (!is_file($xsdFile)) {
            return array('valid' => false, 'errors' => array('XML Schema file not found: ' . $xsdFile));
        }

        $previous = libxml_use_internal_errors(true);
        libxml_clear_errors();

        $document = new DOMDocument();
        $document->loadXML($xml);
        $valid = $document->schemaValidate($xsdFile);

        $errors = array();
        foreach (libxml_get_errors() as $error) {
            $errors[] = trim($error->message) . ' (line ' . $error->line . ')';
        }
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        return array('valid' => (bool)$valid, 'errors' => $errors);
    }
}
