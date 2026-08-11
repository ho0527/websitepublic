<?php
/**
 * 首頁、登入、登出與錯誤頁。
 */
class SiteController extends Controller
{
    /**
     * 首頁與登入頁允許訪客瀏覽，其餘功能仍需登入。
     */
    public function accessRules()
    {
        return array(
            array('allow', 'actions' => array('index', 'login', 'logout', 'error'), 'users' => array('*')),
            array('allow', 'users' => array('@')),
            array('deny',  'users' => array('*')),
        );
    }

    public function actionIndex()
    {
        $summary = array();
        if (!Yii::app()->user->isGuest) {
            $summary = array(
                'Lines'    => Line::model()->count(),
                'Stations' => Station::model()->count(),
                'Vehicles' => Vehicle::model()->count(),
                'Drivers'  => Driver::model()->count(),
            );
        }
        $this->render('index', array('summary' => $summary));
    }

    public function actionLogin()
    {
        if (!Yii::app()->user->isGuest) {
            $this->redirect(array('site/index'));
        }

        $model = new LoginForm();
        if (isset($_POST['LoginForm'])) {
            $model->attributes = $_POST['LoginForm'];
            if ($model->validate() && $model->login()) {
                $this->redirect(array('site/index'));
            }
        }

        $this->breadcrumbs = array('Login');
        $this->render('login', array('model' => $model));
    }

    public function actionLogout()
    {
        Yii::app()->user->logout();
        $this->redirect(array('site/index'));
    }

    /**
     * 統一的錯誤畫面。
     */
    public function actionError()
    {
        $error = Yii::app()->errorHandler->error;
        if ($error) {
            if (Yii::app()->request->isAjaxRequest) {
                echo $error['message'];
            } else {
                $this->render('error', array('error' => $error));
            }
        }
    }
}
