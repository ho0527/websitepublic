<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\ServiceContainer;
use App\Models\Station;
use App\Models\TrainType;

/**
 * 首頁與車次查詢。
 */
final class HomeController extends Controller
{
    /** 車種下拉選單中代表「全部車種」的值 */
    private const ALL_TRAIN_TYPES = 'all';

    /**
     * 首頁：提供車次查詢表單。
     */
    public function index(): void
    {
        $this->render('front/home', [
            'title'      => '首頁',
            'stations'   => Station::allOrdered(),
            'trainTypes' => TrainType::allOrdered(),
            'today'      => (new \DateTimeImmutable())->format('Y-m-d'),
        ]);
    }

    /**
     * 首頁表單以 GET 送出後，改導向 SEO 形式的網址。
     */
    public function redirectToSeoUrl(): void
    {
        $date      = $this->request->query('date', '') ?? '';
        $from      = $this->request->query('from', '') ?? '';
        $to        = $this->request->query('to', '') ?? '';
        $trainType = $this->request->query('trainType', self::ALL_TRAIN_TYPES) ?? self::ALL_TRAIN_TYPES;

        $this->redirect(sprintf(
            'train-lookup/%s/%s/%s/%s',
            rawurlencode($date),
            rawurlencode($from),
            rawurlencode($to),
            rawurlencode($trainType === '' ? self::ALL_TRAIN_TYPES : $trainType)
        ));
    }

    /**
     * 車次查詢結果。
     *
     * @param string $date      搭乘日期（YYYY-MM-DD）
     * @param string $from      起程站英文代碼
     * @param string $to        到達站英文代碼
     * @param string $trainType 車種編號，all 代表全部車種
     */
    public function lookup(string $date, string $from, string $to, string $trainType): void
    {
        $stations   = Station::allOrdered();
        $trainTypes = TrainType::allOrdered();

        $fromStation = Station::findByCode($from);
        $toStation   = Station::findByCode($to);
        $travelDate  = \DateTimeImmutable::createFromFormat('!Y-m-d', $date);

        $conditions = [
            'date'      => $date,
            'from'      => $from,
            'to'        => $to,
            'trainType' => $trainType,
        ];

        // 條件不完整或不合法時，直接顯示提示訊息請使用者修改條件
        if ($fromStation === null || $toStation === null || $travelDate === false) {
            $this->renderResults($stations, $trainTypes, $conditions, [], '查詢條件不正確，請重新選擇起程站、到達站與搭乘日期');

            return;
        }

        if ($fromStation->id() === $toStation->id()) {
            $this->renderResults($stations, $trainTypes, $conditions, [], '起程站與到達站不可相同，請修改條件後繼續查詢');

            return;
        }

        $trainTypeId = $trainType === self::ALL_TRAIN_TYPES ? null : (int) $trainType;
        $results     = ServiceContainer::trainLookup()->search($fromStation, $toStation, $trainTypeId, $travelDate);

        $message = $results === []
            ? sprintf(
                '查無 %s 由 %s 站到 %s 站的車次，請修改起訖站、車種或搭乘日期後繼續查詢',
                $travelDate->format('Y/m/d'),
                $fromStation->name,
                $toStation->name
            )
            : null;

        $this->renderResults($stations, $trainTypes, $conditions, $results, $message, $fromStation, $toStation, $travelDate);
    }

    /**
     * 渲染查詢結果頁。
     *
     * @param array<int, Station>        $stations
     * @param array<int, TrainType>      $trainTypes
     * @param array<string, string>      $conditions
     * @param array<int, array<mixed>>   $results
     */
    private function renderResults(
        array $stations,
        array $trainTypes,
        array $conditions,
        array $results,
        ?string $emptyMessage,
        ?Station $fromStation = null,
        ?Station $toStation = null,
        ?\DateTimeImmutable $travelDate = null
    ): void {
        $this->render('front/train-lookup', [
            'title'        => '車次查詢結果',
            'stations'     => $stations,
            'trainTypes'   => $trainTypes,
            'conditions'   => $conditions,
            'results'      => $results,
            'emptyMessage' => $emptyMessage,
            'fromStation'  => $fromStation,
            'toStation'    => $toStation,
            'travelDate'   => $travelDate,
        ]);
    }
}
