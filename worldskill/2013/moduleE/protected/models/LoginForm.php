<?php
/**
 * 登入表單模型（不對應資料表）。
 */
class LoginForm extends CFormModel
{
    public $username;
    public $password;

    /** @var UserIdentity */
    private $_identity;

    public function rules()
    {
        return array(
            array('username, password', 'required'),
            array('password', 'authenticate'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'username' => 'Login',
            'password' => 'Password',
        );
    }

    /**
     * 驗證帳號密碼是否正確。
     */
    public function authenticate($attribute, $params)
    {
        if ($this->hasErrors()) {
            return;
        }
        $this->_identity = new UserIdentity($this->username, $this->password);
        if (!$this->_identity->authenticate()) {
            $this->addError('password', 'Incorrect login or password.');
        }
    }

    /**
     * 驗證通過後建立登入狀態。
     */
    public function login()
    {
        if ($this->_identity === null) {
            $this->_identity = new UserIdentity($this->username, $this->password);
            $this->_identity->authenticate();
        }
        if ($this->_identity->errorCode === UserIdentity::ERROR_NONE) {
            Yii::app()->user->login($this->_identity, 0);
            return true;
        }
        return false;
    }
}
