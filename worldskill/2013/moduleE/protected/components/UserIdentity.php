<?php
/**
 * 後台管理者身分驗證。
 * 題目提供的資料庫以 md5 儲存密碼（webmaster / leipzig）。
 */
class UserIdentity extends CUserIdentity
{
    /** @var int 登入成功後的使用者 id */
    private $_id;

    public function authenticate()
    {
        $user = User::model()->find('login = :login', array(':login' => $this->username));

        if ($user === null) {
            $this->errorCode = self::ERROR_USERNAME_INVALID;
        } elseif ($user->password !== md5($this->password)) {
            $this->errorCode = self::ERROR_PASSWORD_INVALID;
        } else {
            $this->_id = $user->id;
            $this->setState('displayName', $user->name);
            $this->errorCode = self::ERROR_NONE;
        }

        return $this->errorCode === self::ERROR_NONE;
    }

    public function getId()
    {
        return $this->_id;
    }
}
