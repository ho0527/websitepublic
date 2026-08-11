<?php
/**
 * 所有控制器的共同基底類別。
 * 負責統一版型、麵包屑與右側 Operations 選單。
 */
class Controller extends CController
{
    /** 預設版型 */
    public $layout = '//layouts/main';

    /** 右側 Operations 區塊的連結，格式 array('標題' => array('route')) */
    public $operations = array();

    /** 麵包屑，格式 array('Line' => array('line/index'), 'Create') */
    public $breadcrumbs = array();

    /**
     * 只有登入後才能使用系統功能（首頁與登入頁除外）。
     */
    public function filters()
    {
        return array('accessControl');
    }

    public function accessRules()
    {
        return array(
            array('allow', 'users' => array('@')),     // 已登入者可用
            array('deny',  'users' => array('*')),     // 其餘一律拒絕
        );
    }

    /**
     * 產生本模組自有靜態資源的網址。
     */
    public function asset($path)
    {
        return Yii::app()->request->baseUrl . '/assets/' . ltrim($path, '/');
    }

    /**
     * 於畫面上方顯示一次性訊息。
     */
    protected function flash($type, $message)
    {
        Yii::app()->user->setFlash($type, $message);
    }

    /**
     * 依主鍵載入模型，找不到時丟出 404。
     *
     * @param string $modelClass 模型類別名稱
     * @param mixed  $id         主鍵
     * @return CActiveRecord
     */
    protected function loadModel($modelClass, $id)
    {
        $model = CActiveRecord::model($modelClass)->findByPk((int)$id);
        if ($model === null) {
            throw new CHttpException(404, 'The requested record does not exist.');
        }
        return $model;
    }
}
