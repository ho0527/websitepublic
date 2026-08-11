<?php
/**
 * 站點（Station）。
 *
 * 資料表 station：id, name, position_station, line_id
 * position_station 為 START / INTER / END，未指派路線時為空字串。
 */
class Station extends CActiveRecord
{
    /** 站點在路線中的位置 */
    const POS_START = 'START';
    const POS_INTER = 'INTER';
    const POS_END   = 'END';

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'station';
    }

    public function rules()
    {
        return array(
            array('name', 'required'),
            array('name', 'length', 'max' => 80),
            array('name', 'unique', 'message' => 'This station name already exists.'),
            array('position_station', 'in',
                  'range' => array('', self::POS_START, self::POS_INTER, self::POS_END)),
            array('line_id', 'numerical', 'integerOnly' => true),
            array('id, name, line_id', 'safe', 'on' => 'search'),
        );
    }

    public function relations()
    {
        return array(
            'line' => array(self::BELONGS_TO, 'Line', 'line_id'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'id'               => 'ID',
            'name'             => 'Name',
            'position_station' => 'Position',
            'line_id'          => 'Line',
        );
    }

    /**
     * 尚未指派給任何路線的站點。
     *
     * @param int $includeLineId 額外納入此路線目前已指派的站點（供編輯用）
     * @return Station[]
     */
    public static function available($includeLineId = 0)
    {
        $criteria = new CDbCriteria();
        $criteria->condition = 'line_id = 0';
        if ($includeLineId > 0) {
            $criteria->condition .= ' OR line_id = :lineId';
            $criteria->params[':lineId'] = (int)$includeLineId;
        }
        $criteria->order = 'name';
        return self::model()->findAll($criteria);
    }

    /**
     * 路線名稱（未指派時顯示 -）。
     */
    public function getLineLabel()
    {
        return $this->line === null ? '-' : $this->line->code;
    }

    /**
     * 解除與路線的關聯。
     */
    public function detach()
    {
        $this->line_id = 0;
        $this->position_station = '';
        return $this->save(false);
    }

    public function search()
    {
        $criteria = new CDbCriteria();
        $criteria->compare('id', $this->id);
        $criteria->compare('name', $this->name, true);
        $criteria->order = 'name';
        return new CActiveDataProvider($this, array(
            'criteria'   => $criteria,
            'pagination' => false,
        ));
    }
}
