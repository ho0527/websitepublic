<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\ServiceContainer;
use App\Models\TrainType;

/**
 * 搭乘人數統計圖表與開放資料。
 */
final class OpenDataController extends Controller
{
    /**
     * 統計圖表頁：可依時間與車種過濾，圖表上方提供 JSON 超連結。
     */
    public function chart(): void
    {
        $filters = $this->currentFilters();
        $data    = ServiceContainer::statistics()->build(
            $filters['train_type_id'],
            $filters['from_date'],
            $filters['to_date'],
            new \DateTimeImmutable()
        );

        $this->render('front/statistics', [
            'title'      => '搭乘人數統計',
            'trainTypes' => TrainType::allOrdered(),
            'filters'    => $filters,
            'data'       => $data,
            // 圖表上方的 JSON 超連結，帶著相同的過濾條件
            'jsonUrl'    => $this->view->url('statistics/export.json') . '?' . http_build_query(array_filter([
                'trainType' => $filters['train_type_id'],
                'from'      => $filters['from_date'],
                'to'        => $filters['to_date'],
            ], static fn ($value): bool => $value !== null && $value !== '')),
            'yesterday'  => (new \DateTimeImmutable('yesterday'))->format('Y-m-d'),
        ]);
    }

    /**
     * 開放資料：以 JSON 格式輸出統計結果，方便機器解讀。
     */
    public function export(): void
    {
        $filters = $this->currentFilters();
        $data    = ServiceContainer::statistics()->build(
            $filters['train_type_id'],
            $filters['from_date'],
            $filters['to_date'],
            new \DateTimeImmutable()
        );

        // 以檔名帶出統計日期，與試題素材 export_2016-08-09.json 的命名一致
        $exportDate = $filters['to_date'] ?? (new \DateTimeImmutable('yesterday'))->format('Y-m-d');

        header('Content-Type: application/json; charset=utf-8');
        header(sprintf('Content-Disposition: inline; filename="export_%s.json"', $exportDate));

        echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

        exit;
    }

    /**
     * 取得目前的過濾條件。
     *
     * @return array{train_type_id: int|null, from_date: string|null, to_date: string|null}
     */
    private function currentFilters(): array
    {
        $trainType = $this->request->query('trainType', '') ?? '';
        $fromDate  = $this->request->query('from', '') ?? '';
        $toDate    = $this->request->query('to', '') ?? '';

        return [
            'train_type_id' => $trainType === '' ? null : (int) $trainType,
            'from_date'     => $this->normaliseDate($fromDate),
            'to_date'       => $this->normaliseDate($toDate),
        ];
    }

    /**
     * 驗證日期格式，不合法時視為未指定。
     */
    private function normaliseDate(string $value): ?string
    {
        if ($value === '') {
            return null;
        }

        $date = \DateTimeImmutable::createFromFormat('!Y-m-d', $value);

        return $date === false ? null : $date->format('Y-m-d');
    }
}
