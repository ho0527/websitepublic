<?php
/**
 * 司機（Driver）管理。
 * 一位司機同時只能被指派到一台車，且車種必須相符。
 */
class DriverController extends Controller
{
    public function actionIndex()
    {
        $drivers = Driver::model()->with('vehicle')->findAll(array('order' => 't.type, t.name'));

        $this->breadcrumbs = array('Driver' => array('driver/index'), 'List');
        $this->operations  = $this->buildOperations();
        $this->render('index', array('drivers' => $drivers));
    }

    public function actionView($id)
    {
        $driver = $this->loadModel('Driver', $id);

        $this->breadcrumbs = array('Driver' => array('driver/index'), $driver->name);
        $this->operations  = $this->buildOperations($driver);
        $this->render('view', array('driver' => $driver));
    }

    public function actionCreate()
    {
        $driver = new Driver();
        $driver->vehicle_id = 0;
        $driver->avatar = 'avatar.png';

        if (isset($_POST['Driver'])) {
            $driver->attributes = $_POST['Driver'];
            $driver->avatarFile = CUploadedFile::getInstance($driver, 'avatarFile');

            if ($driver->validate()) {
                $savedName = UploadHelper::save($driver->avatarFile, Yii::app()->params['uploadAvatarPath']);
                if ($savedName !== null) {
                    $driver->avatar = $savedName;
                }
                if ($driver->save(false)) {
                    $this->flash('success', 'Driver "' . $driver->name . '" has been created.');
                    $this->redirect(array('driver/index'));
                }
            }
        }

        $this->breadcrumbs = array('Driver' => array('driver/index'), 'Create');
        $this->operations  = $this->buildOperations();
        $this->render('create', array('driver' => $driver, 'vehicles' => $this->vehicleOptions($driver)));
    }

    public function actionUpdate($id)
    {
        $driver = $this->loadModel('Driver', $id);

        if (isset($_POST['Driver'])) {
            $driver->attributes = $_POST['Driver'];
            $driver->avatarFile = CUploadedFile::getInstance($driver, 'avatarFile');

            if ($driver->validate()) {
                $savedName = UploadHelper::save($driver->avatarFile, Yii::app()->params['uploadAvatarPath']);
                if ($savedName !== null) {
                    UploadHelper::remove($driver->avatar, Yii::app()->params['uploadAvatarPath'], array('avatar.png'));
                    $driver->avatar = $savedName;
                }
                if ($driver->save(false)) {
                    $this->flash('success', 'Driver "' . $driver->name . '" has been updated.');
                    $this->redirect(array('driver/index'));
                }
            }
        }

        $this->breadcrumbs = array('Driver' => array('driver/index'), $driver->name => array('driver/view', 'id' => $driver->id), 'Update');
        $this->operations  = $this->buildOperations($driver);
        $this->render('update', array('driver' => $driver, 'vehicles' => $this->vehicleOptions($driver)));
    }

    public function actionDelete($id)
    {
        $driver = $this->loadModel('Driver', $id);
        $name = $driver->name;
        UploadHelper::remove($driver->avatar, Yii::app()->params['uploadAvatarPath'], array('avatar.png'));
        $driver->delete();

        $this->flash('success', 'Driver "' . $name . '" has been deleted.');
        $this->redirect(array('driver/index'));
    }

    /**
     * 可指派給此司機的車輛：車種相符者；已被本司機占用的車輛也列入。
     *
     * 註：一台車可以有多位司機（見 XML Schema 的 driver maxOccurs="unbounded"），
     * 限制在於「一位司機只能有一台車」。
     */
    private function vehicleOptions(Driver $driver)
    {
        $vehicles = Vehicle::model()->findAll(array('order' => 'type, name'));
        // 依車種分組，畫面上再以 JavaScript 過濾出與司機車種相符的選項
        return CHtml::listData($vehicles, 'id', 'name', 'type');
    }

    private function buildOperations($driver = null)
    {
        $operations = array(
            'List Driver'   => array('driver/index'),
            'Create Driver' => array('driver/create'),
        );
        if ($driver !== null) {
            $operations['View Driver']   = array('driver/view', 'id' => $driver->id);
            $operations['Update Driver'] = array('driver/update', 'id' => $driver->id);
        }
        return $operations;
    }
}
