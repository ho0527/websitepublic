<?php
/**
 * 車輛（Vehicle）管理。
 */
class VehicleController extends Controller
{
    public function actionIndex()
    {
        $vehicles = Vehicle::model()->with('line', 'drivers')->findAll(array('order' => 't.type, t.name'));

        $this->breadcrumbs = array('Vehicle' => array('vehicle/index'), 'List');
        $this->operations  = $this->buildOperations();
        $this->render('index', array('vehicles' => $vehicles));
    }

    public function actionView($id)
    {
        $vehicle = $this->loadModel('Vehicle', $id);

        $this->breadcrumbs = array('Vehicle' => array('vehicle/index'), $vehicle->name);
        $this->operations  = $this->buildOperations($vehicle);
        $this->render('view', array('vehicle' => $vehicle));
    }

    public function actionCreate()
    {
        $vehicle = new Vehicle();
        $vehicle->line_id = 0;

        if (isset($_POST['Vehicle'])) {
            $vehicle->attributes = $_POST['Vehicle'];
            $vehicle->line_id = 0;   // 新車輛尚未指派路線
            if ($vehicle->save()) {
                $this->flash('success', 'Vehicle "' . $vehicle->name . '" has been created.');
                $this->redirect(array('vehicle/index'));
            }
        }

        $this->breadcrumbs = array('Vehicle' => array('vehicle/index'), 'Create');
        $this->operations  = $this->buildOperations();
        $this->render('create', array('vehicle' => $vehicle));
    }

    public function actionUpdate($id)
    {
        $vehicle = $this->loadModel('Vehicle', $id);
        $previousType = $vehicle->type;

        if (isset($_POST['Vehicle'])) {
            $vehicle->attributes = $_POST['Vehicle'];
            if ($vehicle->validate()) {
                // 車種變更時，必須釋放不再相符的路線與司機
                if ($vehicle->type !== $previousType) {
                    $vehicle->line_id = 0;
                    Yii::app()->db->createCommand()
                        ->update('driver', array('vehicle_id' => 0),
                                 'vehicle_id = :id AND type <> :type',
                                 array(':id' => $vehicle->id, ':type' => $vehicle->type));
                }
                if ($vehicle->save(false)) {
                    $this->flash('success', 'Vehicle "' . $vehicle->name . '" has been updated.');
                    $this->redirect(array('vehicle/index'));
                }
            }
        }

        $this->breadcrumbs = array('Vehicle' => array('vehicle/index'), $vehicle->name => array('vehicle/view', 'id' => $vehicle->id), 'Update');
        $this->operations  = $this->buildOperations($vehicle);
        $this->render('update', array('vehicle' => $vehicle));
    }

    public function actionDelete($id)
    {
        $vehicle = $this->loadModel('Vehicle', $id);
        $name = $vehicle->name;
        $vehicle->delete();

        $this->flash('success', 'Vehicle "' . $name . '" has been deleted and its drivers are available again.');
        $this->redirect(array('vehicle/index'));
    }

    /**
     * 將車輛從所屬路線移除。
     */
    public function actionDetach($id)
    {
        $vehicle = $this->loadModel('Vehicle', $id);
        if ((int)$vehicle->line_id === 0) {
            $this->flash('error', 'This vehicle does not belong to any line.');
        } else {
            $lineCode = $vehicle->getLineLabel();
            $vehicle->line_id = 0;
            $vehicle->save(false);
            $this->flash('success', 'Vehicle "' . $vehicle->name . '" has been removed from line "' . $lineCode . '".');
        }
        $this->redirect(array('vehicle/index'));
    }

    private function buildOperations($vehicle = null)
    {
        $operations = array(
            'List Vehicle'   => array('vehicle/index'),
            'Create Vehicle' => array('vehicle/create'),
        );
        if ($vehicle !== null) {
            $operations['View Vehicle']   = array('vehicle/view', 'id' => $vehicle->id);
            $operations['Update Vehicle'] = array('vehicle/update', 'id' => $vehicle->id);
        }
        return $operations;
    }
}
