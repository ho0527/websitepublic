<?php
/**
 * 中間路線（Intermediate Line）。
 *
 * 資料表 line：id, code, start_time_operation, end_time_operation, type, map
 */
class Line extends CActiveRecord
{
    /** @var CUploadedFile 上傳的路線圖檔（非資料表欄位） */
    public $mapFile;

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'line';
    }

    public function rules()
    {
        return array(
            array('code, start_time_operation, end_time_operation, type', 'required'),
            array('code', 'length', 'max' => 50),
            array('code', 'unique', 'message' => 'This line name has already been taken.'),
            array('type', 'in', 'range' => array_keys(Yii::app()->params['vehicleTypes'])),
            array('start_time_operation, end_time_operation', 'match',
                  'pattern' => '/^([01]\d|2[0-3]):[0-5]\d(:[0-5]\d)?$/',
                  'message' => '{attribute} must be a valid time (HH:MM:SS).'),
            array('map', 'length', 'max' => 50),
            array('mapFile', 'file', 'allowEmpty' => true,
                  'types' => 'svg, png, jpg, jpeg, gif',
                  'maxSize' => 4194304,
                  'tooLarge' => 'The map image must be smaller than 4MB.'),
            array('id, code, type', 'safe', 'on' => 'search'),
        );
    }

    public function relations()
    {
        return array(
            // 依站點順序：起站 -> 中間站 -> 終站
            'stations' => array(self::HAS_MANY, 'Station', 'line_id',
                'order' => "FIELD(stations.position_station,'START','INTER','END'), stations.id"),
            'vehicles' => array(self::HAS_MANY, 'Vehicle', 'line_id', 'order' => 'vehicles.name'),
            'vehicleCount' => array(self::STAT, 'Vehicle', 'line_id'),
            'stationCount' => array(self::STAT, 'Station', 'line_id'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'id'                   => 'ID',
            'code'                 => 'Name',
            'start_time_operation' => 'Start Time Operation',
            'end_time_operation'   => 'End Time Operation',
            'type'                 => 'Type',
            'map'                  => 'Map',
            'mapFile'              => 'Map',
        );
    }

    /**
     * 車種顯示名稱。
     */
    public function getTypeLabel()
    {
        $types = Yii::app()->params['vehicleTypes'];
        return isset($types[$this->type]) ? $types[$this->type] : $this->type;
    }

    /**
     * 路線圖的網址；未上傳時回傳 null。
     */
    public function getMapUrl()
    {
        if (empty($this->map)) {
            return null;
        }
        return Yii::app()->request->baseUrl . '/' . Yii::app()->params['uploadMapUrl'] . '/' . rawurlencode($this->map);
    }

    /**
     * 取得指定位置的站點（START / END）。
     */
    public function getStationAt($position)
    {
        foreach ($this->stations as $station) {
            if ($station->position_station === $position) {
                return $station;
            }
        }
        return null;
    }

    /**
     * 取得中間站（依 id 排序）。
     */
    public function getIntermediateStations()
    {
        $result = array();
        foreach ($this->stations as $station) {
            if ($station->position_station === 'INTER') {
                $result[] = $station;
            }
        }
        return $result;
    }

    /**
     * 產生小時級的時間下拉選單資料（00:00:00 ~ 23:00:00）。
     */
    public static function hourOptions()
    {
        $options = array();
        for ($h = 0; $h < 24; $h++) {
            $value = sprintf('%02d:00:00', $h);
            $options[$value] = $value;
        }
        return $options;
    }

    /**
     * 刪除路線前先解除車輛與站點的關聯，避免出現孤兒資料。
     */
    protected function beforeDelete()
    {
        if (!parent::beforeDelete()) {
            return false;
        }
        Yii::app()->db->createCommand()
            ->update('vehicle', array('line_id' => 0), 'line_id = :id', array(':id' => $this->id));
        Yii::app()->db->createCommand()
            ->update('station', array('line_id' => 0, 'position_station' => ''), 'line_id = :id', array(':id' => $this->id));
        return true;
    }

    /**
     * 供列表使用的搜尋 DataProvider。
     */
    public function search()
    {
        $criteria = new CDbCriteria();
        $criteria->compare('id', $this->id);
        $criteria->compare('code', $this->code, true);
        $criteria->compare('type', $this->type, true);
        $criteria->order = 'code';
        return new CActiveDataProvider($this, array(
            'criteria'   => $criteria,
            'pagination' => false,
        ));
    }
}
