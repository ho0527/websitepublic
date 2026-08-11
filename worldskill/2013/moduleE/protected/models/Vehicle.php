<?php
/**
 * 車輛（Vehicle）。
 *
 * 資料表 vehicle：id, name, capacity, type, line_id
 * 一台車最多只能屬於一條中間路線，且車種必須與路線相同。
 */
class Vehicle extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'vehicle';
    }

    public function rules()
    {
        return array(
            array('name, capacity, type', 'required'),
            array('name', 'length', 'max' => 30),
            array('name', 'unique', 'message' => 'This vehicle name already exists.'),
            array('capacity', 'numerical', 'integerOnly' => true, 'min' => 1, 'max' => 1000),
            array('type', 'in', 'range' => array_keys(Yii::app()->params['vehicleTypes'])),
            array('line_id', 'numerical', 'integerOnly' => true),
            array('id, name, type, line_id', 'safe', 'on' => 'search'),
        );
    }

    public function relations()
    {
        return array(
            'line'    => array(self::BELONGS_TO, 'Line', 'line_id'),
            'drivers' => array(self::HAS_MANY, 'Driver', 'vehicle_id', 'order' => 'drivers.name'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'id'       => 'ID',
            'name'     => 'Name',
            'capacity' => 'Capacity',
            'type'     => 'Type',
            'line_id'  => 'Line',
        );
    }

    /**
     * 尚未指派路線、且車種符合的車輛。
     *
     * @param string $type          車種
     * @param int    $includeLineId 額外納入此路線目前已指派的車輛
     * @return Vehicle[]
     */
    public static function available($type, $includeLineId = 0)
    {
        $criteria = new CDbCriteria();
        $criteria->condition = 'type = :type AND (line_id = 0';
        $criteria->params[':type'] = $type;
        if ($includeLineId > 0) {
            $criteria->condition .= ' OR line_id = :lineId';
            $criteria->params[':lineId'] = (int)$includeLineId;
        }
        $criteria->condition .= ')';
        $criteria->order = 'name';
        return self::model()->findAll($criteria);
    }

    public function getTypeLabel()
    {
        $types = Yii::app()->params['vehicleTypes'];
        return isset($types[$this->type]) ? $types[$this->type] : $this->type;
    }

    public function getLineLabel()
    {
        return $this->line === null ? '-' : $this->line->code;
    }

    /**
     * 刪除車輛前先讓其司機回到可用狀態。
     */
    protected function beforeDelete()
    {
        if (!parent::beforeDelete()) {
            return false;
        }
        Yii::app()->db->createCommand()
            ->update('driver', array('vehicle_id' => 0), 'vehicle_id = :id', array(':id' => $this->id));
        return true;
    }

    public function search()
    {
        $criteria = new CDbCriteria();
        $criteria->compare('id', $this->id);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('type', $this->type);
        $criteria->order = 'type, name';
        return new CActiveDataProvider($this, array(
            'criteria'   => $criteria,
            'pagination' => false,
        ));
    }
}
