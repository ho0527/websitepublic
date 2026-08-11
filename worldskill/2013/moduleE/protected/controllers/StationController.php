<?php
/**
 * 站點（Station）管理。
 */
class StationController extends Controller
{
    public function actionIndex()
    {
        $stations = Station::model()->with('line')->findAll(array('order' => 't.name'));

        $this->breadcrumbs = array('Station' => array('station/index'), 'List');
        $this->operations  = $this->buildOperations();
        $this->render('index', array('stations' => $stations));
    }

    public function actionView($id)
    {
        $station = $this->loadModel('Station', $id);

        $this->breadcrumbs = array('Station' => array('station/index'), $station->name);
        $this->operations  = $this->buildOperations($station);
        $this->render('view', array('station' => $station));
    }

    public function actionCreate()
    {
        $station = new Station();
        $station->line_id = 0;
        $station->position_station = '';

        if (isset($_POST['Station'])) {
            $station->attributes = $_POST['Station'];
            $station->line_id = 0;             // 新站點一律為未指派狀態
            $station->position_station = '';
            if ($station->save()) {
                $this->flash('success', 'Station "' . $station->name . '" has been created.');
                $this->redirect(array('station/index'));
            }
        }

        $this->breadcrumbs = array('Station' => array('station/index'), 'Create');
        $this->operations  = $this->buildOperations();
        $this->render('create', array('station' => $station));
    }

    public function actionUpdate($id)
    {
        $station = $this->loadModel('Station', $id);

        if (isset($_POST['Station'])) {
            $station->name = $_POST['Station']['name'];
            if ($station->save()) {
                $this->flash('success', 'Station "' . $station->name . '" has been updated.');
                $this->redirect(array('station/index'));
            }
        }

        $this->breadcrumbs = array('Station' => array('station/index'), $station->name => array('station/view', 'id' => $station->id), 'Update');
        $this->operations  = $this->buildOperations($station);
        $this->render('update', array('station' => $station));
    }

    public function actionDelete($id)
    {
        $station = $this->loadModel('Station', $id);
        $name = $station->name;
        $station->delete();

        $this->flash('success', 'Station "' . $name . '" has been deleted.');
        $this->redirect(array('station/index'));
    }

    /**
     * 將站點從所屬路線中移除（路線會因此不足 7 站，需重新指派）。
     */
    public function actionDetach($id)
    {
        $station = $this->loadModel('Station', $id);
        if ((int)$station->line_id === 0) {
            $this->flash('error', 'This station does not belong to any line.');
        } else {
            $lineCode = $station->getLineLabel();
            $station->detach();
            $this->flash('success', 'Station "' . $station->name . '" has been removed from line "' . $lineCode . '".');
        }
        $this->redirect(array('station/index'));
    }

    private function buildOperations($station = null)
    {
        $operations = array(
            'List Station'   => array('station/index'),
            'Create Station' => array('station/create'),
        );
        if ($station !== null) {
            $operations['View Station']   = array('station/view', 'id' => $station->id);
            $operations['Update Station'] = array('station/update', 'id' => $station->id);
        }
        return $operations;
    }
}
