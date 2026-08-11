<?php
/**
 * 管理者帳號（User）管理。
 * 已登入的管理者可以再建立其他管理者。
 */
class UserController extends Controller
{
    public function actionIndex()
    {
        $users = User::model()->findAll(array('order' => 'login'));

        $this->breadcrumbs = array('User' => array('user/index'), 'List');
        $this->operations  = $this->buildOperations();
        $this->render('index', array('users' => $users));
    }

    public function actionCreate()
    {
        $user = new User('insert');

        if (isset($_POST['User'])) {
            $user->attributes  = $_POST['User'];
            $user->newPassword = isset($_POST['User']['newPassword']) ? $_POST['User']['newPassword'] : '';
            if ($user->save()) {
                $this->flash('success', 'Administrator "' . $user->login . '" has been created.');
                $this->redirect(array('user/index'));
            }
        }

        $this->breadcrumbs = array('User' => array('user/index'), 'Create');
        $this->operations  = $this->buildOperations();
        $this->render('create', array('user' => $user));
    }

    public function actionUpdate($id)
    {
        $user = $this->loadModel('User', $id);
        $user->setScenario('update');

        if (isset($_POST['User'])) {
            $user->attributes  = $_POST['User'];
            $user->newPassword = isset($_POST['User']['newPassword']) ? $_POST['User']['newPassword'] : '';
            if ($user->save()) {
                $this->flash('success', 'Administrator "' . $user->login . '" has been updated.');
                $this->redirect(array('user/index'));
            }
        }

        $user->newPassword = '';
        $this->breadcrumbs = array('User' => array('user/index'), $user->login => array('user/index'), 'Update');
        $this->operations  = $this->buildOperations();
        $this->render('update', array('user' => $user));
    }

    public function actionDelete($id)
    {
        $user = $this->loadModel('User', $id);

        // 不允許刪除自己，也不允許刪掉最後一個管理者
        if ((int)$user->id === (int)Yii::app()->user->id) {
            $this->flash('error', 'You cannot delete the account you are logged in with.');
        } elseif (User::model()->count() <= 1) {
            $this->flash('error', 'The system must keep at least one administrator.');
        } else {
            $login = $user->login;
            $user->delete();
            $this->flash('success', 'Administrator "' . $login . '" has been deleted.');
        }
        $this->redirect(array('user/index'));
    }

    private function buildOperations()
    {
        return array(
            'List User'   => array('user/index'),
            'Create User' => array('user/create'),
        );
    }
}
