<?php
/**
 * 司機（Driver）。
 *
 * 資料表 driver：id, name, birth_date, email, phone, avatar, type, vehicle_id
 * 一位司機同時間只能被指派到一台車，且只能駕駛與自己車種相符的車輛。
 */
class Driver extends CActiveRecord
{
    /** @var CUploadedFile 上傳的大頭貼（非資料表欄位） */
    public $avatarFile;

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'driver';
    }

    public function rules()
    {
        return array(
            array('name, birth_date, email, phone, type', 'required'),
            array('name', 'length', 'max' => 45),
            array('email', 'email'),
            array('email', 'length', 'max' => 50),
            array('phone', 'length', 'max' => 40),
            array('phone', 'match', 'pattern' => '/^[0-9 +()\-]+$/',
                  'message' => 'Phone may only contain digits, spaces and + ( ) -'),
            array('birth_date', 'IsoDateValidator'),
            array('type', 'in', 'range' => array_keys(Yii::app()->params['vehicleTypes'])),
            array('vehicle_id', 'numerical', 'integerOnly' => true),
            array('vehicle_id', 'validateVehicle'),
            array('avatarFile', 'file', 'allowEmpty' => true,
                  'types' => 'png, jpg, jpeg, gif',
                  'maxSize' => 2097152,
                  'tooLarge' => 'The avatar must be smaller than 2MB.'),
            array('id, name, type, vehicle_id', 'safe', 'on' => 'search'),
        );
    }

    /**
     * 指派車輛時，車種必須與司機的車種相符。
     */
    public function validateVehicle($attribute)
    {
        if (empty($this->vehicle_id)) {
            return;
        }
        $vehicle = Vehicle::model()->findByPk($this->vehicle_id);
        if ($vehicle === null) {
            $this->addError($attribute, 'The selected vehicle does not exist.');
        } elseif ($vehicle->type !== $this->type) {
            $this->addError($attribute, 'A ' . $this->type . ' driver can only be assigned to a ' . $this->type . '.');
        }
    }

    public function relations()
    {
        return array(
            'vehicle' => array(self::BELONGS_TO, 'Vehicle', 'vehicle_id'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'id'         => 'ID',
            'name'       => 'Name',
            'birth_date' => 'Birth Date',
            'email'      => 'Email',
            'phone'      => 'Phone',
            'avatar'     => 'Avatar',
            'avatarFile' => 'Avatar',
            'type'       => 'Type Vehicle',
            'vehicle_id' => 'Vehicle',
        );
    }

    public function getTypeLabel()
    {
        $types = Yii::app()->params['vehicleTypes'];
        return isset($types[$this->type]) ? $types[$this->type] : $this->type;
    }

    /**
     * 大頭貼網址；沒有檔案時退回預設 avatar.png。
     */
    public function getAvatarUrl()
    {
        $file = $this->avatar !== '' ? $this->avatar : 'avatar.png';
        return Yii::app()->request->baseUrl . '/' . Yii::app()->params['uploadAvatarUrl'] . '/' . rawurlencode($file);
    }

    public function getVehicleLabel()
    {
        return $this->vehicle === null ? '-' : $this->vehicle->name;
    }

    /**
     * 指定車種下，尚未被指派車輛的司機。
     *
     * @param string $type             車種
     * @param int    $includeVehicleId 額外納入目前已指派此車的司機
     * @return Driver[]
     */
    public static function available($type, $includeVehicleId = 0)
    {
        $criteria = new CDbCriteria();
        $criteria->condition = 'type = :type AND (vehicle_id = 0';
        $criteria->params[':type'] = $type;
        if ($includeVehicleId > 0) {
            $criteria->condition .= ' OR vehicle_id = :vid';
            $criteria->params[':vid'] = (int)$includeVehicleId;
        }
        $criteria->condition .= ')';
        $criteria->order = 'name';
        return self::model()->findAll($criteria);
    }

    public function search()
    {
        $criteria = new CDbCriteria();
        $criteria->compare('id', $this->id);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('type', $this->type);
        $criteria->order = 'name';
        return new CActiveDataProvider($this, array(
            'criteria'   => $criteria,
            'pagination' => false,
        ));
    }
}
