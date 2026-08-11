<?php
/**
 * 管理者帳號（User）。
 *
 * 資料表 user：id, name, gender, birth_date, email, login, password
 * 密碼沿用題目資料庫的 md5 格式（webmaster / leipzig）。
 */
class User extends CActiveRecord
{
    /** @var string 表單輸入的明碼密碼（非資料表欄位） */
    public $newPassword;

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'user';
    }

    public function rules()
    {
        return array(
            array('name, gender, birth_date, email, login', 'required'),
            array('newPassword', 'required', 'on' => 'insert'),
            array('newPassword', 'length', 'min' => 4, 'max' => 40),
            array('name', 'length', 'max' => 50),
            array('gender', 'in', 'range' => array('M', 'F')),
            array('birth_date', 'IsoDateValidator'),
            array('email', 'email'),
            array('email', 'length', 'max' => 50),
            array('login', 'length', 'max' => 40),
            array('login', 'unique', 'message' => 'This login is already in use.'),
            array('id, name, login', 'safe', 'on' => 'search'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'id'          => 'ID',
            'name'        => 'Name',
            'gender'      => 'Gender',
            'birth_date'  => 'Birth Date',
            'email'       => 'Email',
            'login'       => 'Login',
            'password'    => 'Password',
            'newPassword' => 'Password',
        );
    }

    public static function genderOptions()
    {
        return array('M' => 'Male', 'F' => 'Female');
    }

    public function getGenderLabel()
    {
        $options = self::genderOptions();
        return isset($options[$this->gender]) ? $options[$this->gender] : $this->gender;
    }

    /**
     * 儲存前把明碼密碼轉為 md5。
     */
    protected function beforeSave()
    {
        if (!parent::beforeSave()) {
            return false;
        }
        if ($this->newPassword !== null && $this->newPassword !== '') {
            $this->password = md5($this->newPassword);
        }
        return true;
    }

    public function search()
    {
        $criteria = new CDbCriteria();
        $criteria->compare('id', $this->id);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('login', $this->login, true);
        $criteria->order = 'login';
        return new CActiveDataProvider($this, array(
            'criteria'   => $criteria,
            'pagination' => false,
        ));
    }
}
