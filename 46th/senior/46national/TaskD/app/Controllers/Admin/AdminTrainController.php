<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Core\ServiceContainer;
use App\Core\Session;
use App\Models\Station;
use App\Models\Train;
use App\Models\TrainServiceDay;
use App\Models\TrainType;

/**
 * 後台列車管理。
 */
final class AdminTrainController extends Controller
{
    /**
     * 列車列表。
     */
    public function index(): void
    {
        $this->requireAdmin();

        $trains   = Train::active()->orderBy('code')->get();
        $stations = Station::keyedById();
        $rows     = [];

        foreach ($trains as $train) {
            $stopNames = [];

            foreach ($train->stops() as $stop) {
                $stopNames[] = (string) ($stations[(int) $stop->station_id]->name ?? '');
            }

            $rows[] = [
                'train'      => $train,
                'type_name'  => (string) ($train->type()?->name ?? ''),
                'route_text' => implode(' → ', $stopNames),
                'weekdays'   => $train->serviceWeekdays(),
            ];
        }

        $this->renderAdmin('admin/trains/index', [
            'title'        => '列車管理',
            'rows'         => $rows,
            'weekdayNames' => TrainServiceDay::allNames(),
            'errors'       => Session::pullFlash('errors', []),
            'notice'       => Session::pullFlash('notice'),
        ]);
    }

    /**
     * 新增列車的表單。
     */
    public function create(): void
    {
        $this->requireAdmin();

        $this->renderTrainForm('新增列車', null);
    }

    /**
     * 寫入新列車。
     */
    public function store(): void
    {
        $this->requireAdmin();

        $input  = $this->collectInput();
        $errors = $this->validate($input, null);

        if ($errors !== []) {
            $this->redirectWithErrors('admin/trains/create', $errors, $input);
        }

        Database::instance()->transaction(function () use ($input): void {
            $train = Train::create([
                'train_type_id' => (int) $input['train_type_id'],
                'code'          => $input['code'],
                'depart_time'   => $input['depart_time'] . ':00',
            ]);

            $train->replaceServiceWeekdays($input['weekdays']);
            $train->replaceStops($input['stops']);
        });

        Session::flash('notice', sprintf('已新增車次「%s」', $input['code']));
        $this->redirect('admin/trains');
    }

    /**
     * 編輯列車的表單。
     */
    public function edit(string $id): void
    {
        $this->requireAdmin();

        $train = Train::find((int) $id);

        if ($train === null || $train->isRemoved()) {
            $this->redirectWithErrors('admin/trains', ['查無此列車']);
        }

        $this->renderTrainForm('編輯列車', $train);
    }

    /**
     * 更新列車。
     */
    public function update(string $id): void
    {
        $this->requireAdmin();

        $train = Train::find((int) $id);

        if ($train === null || $train->isRemoved()) {
            $this->redirectWithErrors('admin/trains', ['查無此列車']);
        }

        $input  = $this->collectInput();
        $errors = $this->validate($input, $train->id());

        if ($errors !== []) {
            $this->redirectWithErrors(sprintf('admin/trains/%d/edit', $train->id()), $errors, $input);
        }

        Database::instance()->transaction(function () use ($train, $input): void {
            $train->train_type_id = (int) $input['train_type_id'];
            $train->code          = $input['code'];
            $train->depart_time   = $input['depart_time'] . ':00';
            $train->save();

            $train->replaceServiceWeekdays($input['weekdays']);
            $train->replaceStops($input['stops']);
        });

        Session::flash('notice', sprintf('已更新車次「%s」', $train->code));
        $this->redirect('admin/trains');
    }

    /**
     * 刪除前的確認頁：若有未發車的訂票，提醒管理員後再由其決定是否繼續。
     */
    public function confirmDelete(string $id): void
    {
        $this->requireAdmin();

        $train = Train::find((int) $id);

        if ($train === null || $train->isRemoved()) {
            $this->redirectWithErrors('admin/trains', ['查無此列車']);
        }

        $affected = ServiceContainer::trainRemoval()->affectedBookings($train, new \DateTimeImmutable());

        $this->renderAdmin('admin/trains/confirm-delete', [
            'title'    => sprintf('刪除車次 %s', $train->code),
            'train'    => $train,
            'affected' => $affected,
        ]);
    }

    /**
     * 執行刪除；同時取消受影響的訂票並以簡訊通知乘客。
     */
    public function destroy(string $id): void
    {
        $this->requireAdmin();

        $train = Train::find((int) $id);

        if ($train === null || $train->isRemoved()) {
            $this->redirectWithErrors('admin/trains', ['查無此列車']);
        }

        $code           = (string) $train->code;
        $cancelledCount = ServiceContainer::trainRemoval()->remove($train, new \DateTimeImmutable());

        Session::flash('notice', $cancelledCount > 0
            ? sprintf('已刪除車次「%s」，並取消 %d 筆訂票、已發送簡訊通知乘客', $code, $cancelledCount)
            : sprintf('已刪除車次「%s」', $code));

        $this->redirect('admin/trains');
    }

    /**
     * 渲染列車表單，帶入既有資料或先前輸入的內容。
     */
    private function renderTrainForm(string $title, ?Train $train): void
    {
        $old = Session::pullFlash('old', []);

        // 表單上的行經車站列：優先使用驗證失敗時保留的輸入，其次是既有資料
        if (isset($old['stops']) && is_array($old['stops']) && $old['stops'] !== []) {
            $stopRows = $old['stops'];
        } elseif ($train !== null) {
            $stopRows = array_map(static fn ($stop): array => [
                'station_id'       => (int) $stop->station_id,
                'travel_minutes'   => (int) $stop->travel_minutes,
                'stop_minutes'     => (int) $stop->stop_minutes,
                'fare_from_origin' => (int) $stop->fare_from_origin,
            ], $train->stops());
        } else {
            // 預設給兩列，對應最少的發車站與終點站
            $stopRows = [
                ['station_id' => 0, 'travel_minutes' => 0, 'stop_minutes' => 0, 'fare_from_origin' => 0],
                ['station_id' => 0, 'travel_minutes' => 0, 'stop_minutes' => 0, 'fare_from_origin' => 0],
            ];
        }

        $this->renderAdmin('admin/trains/form', [
            'title'        => $title,
            'train'        => $train,
            'trainTypes'   => TrainType::allOrdered(),
            'stations'     => Station::allOrdered(),
            'weekdayNames' => TrainServiceDay::allNames(),
            'selectedDays' => $old['weekdays'] ?? ($train?->serviceWeekdays() ?? []),
            'stopRows'     => $stopRows,
            'old'          => $old,
            'errors'       => Session::pullFlash('errors', []),
            'minStops'     => Train::MIN_STOPS,
            'maxStops'     => Train::MAX_STOPS,
        ]);
    }

    /**
     * 收集表單輸入。
     *
     * @return array{
     *     code: string,
     *     train_type_id: string,
     *     depart_time: string,
     *     weekdays: array<int, int>,
     *     stops: array<int, array{station_id: int, travel_minutes: int, stop_minutes: int, fare_from_origin: int}>
     * }
     */
    private function collectInput(): array
    {
        $stationIds     = $this->request->arrayInput('station_id');
        $travelMinutes  = $this->request->arrayInput('travel_minutes');
        $stopMinutes    = $this->request->arrayInput('stop_minutes');
        $fares          = $this->request->arrayInput('fare_from_origin');
        $stops          = [];
        $lastIndex      = count($stationIds) - 1;

        foreach ($stationIds as $index => $stationId) {
            // 未選車站的空白列直接略過，方便表單保留多餘的列
            if ($stationId === '' || (int) $stationId === 0) {
                continue;
            }

            $stops[] = [
                'station_id'       => (int) $stationId,
                // 發車站沒有行駛時間，終點站沒有停留時間
                'travel_minutes'   => $index === 0 ? 0 : max(0, (int) ($travelMinutes[$index] ?? 0)),
                'stop_minutes'     => ($index === 0 || $index === $lastIndex)
                    ? 0
                    : max(0, (int) ($stopMinutes[$index] ?? 0)),
                'fare_from_origin' => $index === 0 ? 0 : max(0, (int) ($fares[$index] ?? 0)),
            ];
        }

        // 重新校正首末站（略過空白列後順序可能改變）
        if ($stops !== []) {
            $stops[0]['travel_minutes'] = 0;
            $stops[0]['stop_minutes']   = 0;
            $stops[0]['fare_from_origin'] = 0;
            $stops[count($stops) - 1]['stop_minutes'] = 0;
        }

        return [
            'code'          => $this->request->input('code', '') ?? '',
            'train_type_id' => $this->request->input('train_type_id', '') ?? '',
            'depart_time'   => $this->request->input('depart_time', '') ?? '',
            'weekdays'      => array_map('intval', $this->request->arrayInput('weekdays')),
            'stops'         => $stops,
        ];
    }

    /**
     * 驗證列車資料。
     *
     * @param array<string, mixed> $input
     * @return array<int, string>
     */
    private function validate(array $input, ?int $exceptId): array
    {
        $errors = [];

        if ($input['code'] === '') {
            $errors[] = '請填寫列車代碼';
        } elseif (Train::codeExists($input['code'], $exceptId)) {
            $errors[] = sprintf('列車代碼「%s」已被使用，請改用其他代碼', $input['code']);
        }

        if ($input['train_type_id'] === '' || TrainType::find((int) $input['train_type_id']) === null) {
            $errors[] = '請選擇車種';
        }

        if (preg_match('/^\d{2}:\d{2}$/', $input['depart_time']) !== 1) {
            $errors[] = '請填寫正確的發車時間（HH:MM）';
        }

        if ($input['weekdays'] === []) {
            $errors[] = '請至少選擇一個行車星期';
        }

        $errors = array_merge($errors, $this->validateStops($input['stops']));

        return $errors;
    }

    /**
     * 驗證行經車站。
     *
     * @param array<int, array{station_id: int, fare_from_origin: int}> $stops
     * @return array<int, string>
     */
    private function validateStops(array $stops): array
    {
        $errors = [];

        if (count($stops) < Train::MIN_STOPS) {
            $errors[] = sprintf('行經車站至少要有 %d 站（發車站與終點站）', Train::MIN_STOPS);

            return $errors;
        }

        if (count($stops) > Train::MAX_STOPS) {
            $errors[] = sprintf('行經車站最多只能有 %d 站', Train::MAX_STOPS);
        }

        $stationIds = array_column($stops, 'station_id');

        // 相同的車站不可重複加入
        if (count($stationIds) !== count(array_unique($stationIds))) {
            $errors[] = '行經車站不可重複選取相同的車站，請修改後再送出';
        }

        $stations = Station::keyedById();

        foreach ($stationIds as $stationId) {
            if (!isset($stations[$stationId])) {
                $errors[] = '行經車站中含有不存在的車站';

                break;
            }
        }

        // 累計票價必須隨著站序遞增，否則區間票價會變成負數
        $previousFare = 0;

        foreach ($stops as $index => $stop) {
            if ($index === 0) {
                continue;
            }

            if ((int) $stop['fare_from_origin'] < $previousFare) {
                $errors[] = '各站的累計票價必須由發車站往終點站遞增，請修改票價設定';

                break;
            }

            $previousFare = (int) $stop['fare_from_origin'];
        }

        return $errors;
    }
}
