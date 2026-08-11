<?php
/**
 * 中間路線（Intermediate Line）管理。
 *
 * 業務規則：
 *  - 一條路線固定 7 個站點（起站、5 個中間站、終站），且必須一次指派完成。
 *  - 一個站點只能屬於一條路線。
 *  - 一條路線最多 10 台車，且車種必須與路線相同；一台車只能屬於一條路線。
 *  - 刪除路線時，其車輛與站點的關聯全部解除。
 */
class LineController extends Controller
{
    /**
     * 路線清單（同時作為 Lines 報表）。
     */
    public function actionIndex()
    {
        $lines = Line::model()->with('stations', 'vehicles')->findAll(array('order' => 't.code'));

        $this->breadcrumbs = array('Line' => array('line/index'), 'List');
        $this->operations  = $this->buildOperations();
        $this->render('index', array('lines' => $lines));
    }

    /**
     * 單一路線的完整檢視（路線圖、站點、車輛）。
     */
    public function actionView($id)
    {
        $line = $this->loadModel('Line', $id);

        $this->breadcrumbs = array('Line' => array('line/index'), $line->code);
        $this->operations  = $this->buildOperations($line);
        $this->render('view', array('line' => $line));
    }

    /**
     * 建立新的中間路線。
     */
    public function actionCreate()
    {
        $line = new Line();

        if (isset($_POST['Line'])) {
            $line->attributes = $_POST['Line'];
            $line->mapFile    = CUploadedFile::getInstance($line, 'mapFile');

            if ($line->validate()) {
                $savedName = UploadHelper::save($line->mapFile, Yii::app()->params['uploadMapPath']);
                if ($savedName !== null) {
                    $line->map = $savedName;
                }
                if ($line->save(false)) {
                    $this->flash('success', 'Intermediate Line "' . $line->code . '" has been created. Now assign its stations and vehicles.');
                    $this->redirect(array('line/stations', 'id' => $line->id));
                }
            }
        }

        $this->breadcrumbs = array('Line' => array('line/index'), 'Create');
        $this->operations  = $this->buildOperations();
        $this->render('create', array('line' => $line));
    }

    /**
     * 修改中間路線。
     */
    public function actionUpdate($id)
    {
        $line = $this->loadModel('Line', $id);
        $previousType = $line->type;

        if (isset($_POST['Line'])) {
            $line->attributes = $_POST['Line'];
            $line->mapFile    = CUploadedFile::getInstance($line, 'mapFile');

            if ($line->validate()) {
                $savedName = UploadHelper::save($line->mapFile, Yii::app()->params['uploadMapPath']);
                if ($savedName !== null) {
                    $line->map = $savedName;
                }
                // 車種改變時，原本已指派、型別不符的車輛必須先釋放
                if ($line->type !== $previousType) {
                    Yii::app()->db->createCommand()
                        ->update('vehicle', array('line_id' => 0),
                                 'line_id = :id AND type <> :type',
                                 array(':id' => $line->id, ':type' => $line->type));
                }
                if ($line->save(false)) {
                    $this->flash('success', 'Intermediate Line "' . $line->code . '" has been updated.');
                    $this->redirect(array('line/view', 'id' => $line->id));
                }
            }
        }

        $this->breadcrumbs = array('Line' => array('line/index'), $line->code => array('line/view', 'id' => $line->id), 'Update');
        $this->operations  = $this->buildOperations($line);
        $this->render('update', array('line' => $line));
    }

    /**
     * 刪除中間路線（同時解除站點與車輛的關聯，見 Line::beforeDelete）。
     */
    public function actionDelete($id)
    {
        $line = $this->loadModel('Line', $id);
        $code = $line->code;
        $line->delete();

        $this->flash('success', 'Intermediate Line "' . $code . '" has been deleted. All its stations and vehicles are available again.');
        $this->redirect(array('line/index'));
    }

    /**
     * 一次指派 7 個站點（起站 + 5 個中間站 + 終站）。
     */
    public function actionStations($id)
    {
        $line  = $this->loadModel('Line', $id);
        $slots = $this->stationSlots();
        $errors = array();

        // 目前已指派的站點，作為表單預設值
        $selected = array_fill(0, count($slots), '');
        $current  = $line->stations;
        $index    = 0;
        foreach ($current as $station) {
            if ($station->position_station === Station::POS_START) {
                $selected[0] = $station->id;
            } elseif ($station->position_station === Station::POS_END) {
                $selected[6] = $station->id;
            }
        }
        foreach ($line->getIntermediateStations() as $station) {
            $index++;
            if ($index <= 5) {
                $selected[$index] = $station->id;
            }
        }

        if (isset($_POST['StationSlots'])) {
            $posted = $_POST['StationSlots'];
            for ($i = 0; $i < count($slots); $i++) {
                $selected[$i] = isset($posted[$i]) ? (int)$posted[$i] : 0;
            }

            $errors = $this->validateStationSlots($selected, $line);

            if (empty($errors)) {
                $this->saveStationSlots($selected, $line);
                $this->flash('success', 'The 7 stations of line "' . $line->code . '" have been assigned.');
                $this->redirect(array('line/view', 'id' => $line->id));
            }
        }

        $this->breadcrumbs = array('Line' => array('line/index'), $line->code => array('line/view', 'id' => $line->id), 'Stations');
        $this->operations  = $this->buildOperations($line);
        $this->render('stations', array(
            'line'      => $line,
            'slots'     => $slots,
            'selected'  => $selected,
            'stations'  => Station::available($line->id),
            'errors'    => $errors,
        ));
    }

    /**
     * 指派車輛（最多 10 台，且車種必須與路線相同）。
     */
    public function actionVehicles($id)
    {
        $line   = $this->loadModel('Line', $id);
        $errors = array();

        $selected = array();
        foreach ($line->vehicles as $vehicle) {
            $selected[] = $vehicle->id;
        }

        if (isset($_POST['submitVehicles'])) {
            $selected = isset($_POST['VehicleIds']) ? array_map('intval', (array)$_POST['VehicleIds']) : array();
            $maximum  = Yii::app()->params['maxVehiclesPerLine'];

            if (count($selected) > $maximum) {
                $errors[] = 'An Intermediate Line can hold a maximum of ' . $maximum . ' vehicles (' . count($selected) . ' selected).';
            }

            // 確認每台車都存在、車種相符、且未被其他路線占用
            foreach ($selected as $vehicleId) {
                $vehicle = Vehicle::model()->findByPk($vehicleId);
                if ($vehicle === null) {
                    $errors[] = 'Vehicle #' . $vehicleId . ' does not exist.';
                } elseif ($vehicle->type !== $line->type) {
                    $errors[] = 'Vehicle "' . $vehicle->name . '" is a ' . $vehicle->type . ' and cannot run on a ' . $line->type . ' line.';
                } elseif ((int)$vehicle->line_id !== 0 && (int)$vehicle->line_id !== (int)$line->id) {
                    $errors[] = 'Vehicle "' . $vehicle->name . '" already belongs to another line.';
                }
            }

            if (empty($errors)) {
                $transaction = Yii::app()->db->beginTransaction();
                try {
                    // 先釋放本路線原有的車輛，再重新指派
                    Yii::app()->db->createCommand()
                        ->update('vehicle', array('line_id' => 0), 'line_id = :id', array(':id' => $line->id));
                    foreach ($selected as $vehicleId) {
                        Yii::app()->db->createCommand()
                            ->update('vehicle', array('line_id' => $line->id), 'id = :id', array(':id' => $vehicleId));
                    }
                    $transaction->commit();
                    $this->flash('success', count($selected) . ' vehicle(s) assigned to line "' . $line->code . '".');
                    $this->redirect(array('line/view', 'id' => $line->id));
                } catch (Exception $e) {
                    $transaction->rollback();
                    $errors[] = 'Could not save the vehicle assignment: ' . $e->getMessage();
                }
            }
        }

        $this->breadcrumbs = array('Line' => array('line/index'), $line->code => array('line/view', 'id' => $line->id), 'Vehicles');
        $this->operations  = $this->buildOperations($line);
        $this->render('vehicles', array(
            'line'      => $line,
            'vehicles'  => Vehicle::available($line->type, $line->id),
            'selected'  => $selected,
            'errors'    => $errors,
        ));
    }

    // ------------------------------------------------------------------
    // 內部輔助方法
    // ------------------------------------------------------------------

    /**
     * 7 個站點欄位的名稱與位置代碼。
     */
    private function stationSlots()
    {
        return array(
            array('label' => 'Starting Station', 'position' => Station::POS_START),
            array('label' => 'Station 2',        'position' => Station::POS_INTER),
            array('label' => 'Station 3',        'position' => Station::POS_INTER),
            array('label' => 'Station 4',        'position' => Station::POS_INTER),
            array('label' => 'Station 5',        'position' => Station::POS_INTER),
            array('label' => 'Station 6',        'position' => Station::POS_INTER),
            array('label' => 'End Station',      'position' => Station::POS_END),
        );
    }

    /**
     * 檢查 7 個站點欄位：必須全部填寫、不可重複、且不可屬於其他路線。
     *
     * @return string[] 錯誤訊息
     */
    private function validateStationSlots(array $selected, Line $line)
    {
        $errors = array();
        $slots  = $this->stationSlots();

        foreach ($selected as $i => $stationId) {
            if (empty($stationId)) {
                $errors[] = 'All ' . count($slots) . ' stations must be selected at the same time - "' . $slots[$i]['label'] . '" is empty.';
            }
        }
        if (!empty($errors)) {
            return $errors;
        }

        if (count(array_unique($selected)) !== count($selected)) {
            $errors[] = 'Each station can be used only once in a line.';
        }

        foreach ($selected as $stationId) {
            $station = Station::model()->findByPk($stationId);
            if ($station === null) {
                $errors[] = 'Station #' . $stationId . ' does not exist.';
            } elseif ((int)$station->line_id !== 0 && (int)$station->line_id !== (int)$line->id) {
                $errors[] = 'Station "' . $station->name . '" already belongs to another Intermediate Line.';
            }
        }

        return $errors;
    }

    /**
     * 寫入 7 個站點的指派結果。
     */
    private function saveStationSlots(array $selected, Line $line)
    {
        $slots       = $this->stationSlots();
        $transaction = Yii::app()->db->beginTransaction();
        try {
            // 先釋放本路線原有的站點
            Yii::app()->db->createCommand()
                ->update('station', array('line_id' => 0, 'position_station' => ''),
                         'line_id = :id', array(':id' => $line->id));

            foreach ($selected as $i => $stationId) {
                Yii::app()->db->createCommand()
                    ->update('station',
                             array('line_id' => $line->id, 'position_station' => $slots[$i]['position']),
                             'id = :id', array(':id' => (int)$stationId));
            }
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollback();
            throw $e;
        }
    }

    /**
     * 右側 Operations 連結。
     */
    private function buildOperations($line = null)
    {
        $operations = array(
            'List Line'   => array('line/index'),
            'Create Line' => array('line/create'),
        );
        if ($line !== null) {
            $operations['View Line']       = array('line/view', 'id' => $line->id);
            $operations['Update Line']     = array('line/update', 'id' => $line->id);
            $operations['Manage Stations'] = array('line/stations', 'id' => $line->id);
            $operations['Manage Vehicles'] = array('line/vehicles', 'id' => $line->id);
        }
        $operations['Download XML'] = array('xml/download');
        return $operations;
    }
}
