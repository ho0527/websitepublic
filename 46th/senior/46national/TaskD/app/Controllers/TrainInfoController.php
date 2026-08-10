<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\ServiceContainer;
use App\Models\Train;
use App\Models\TrainServiceDay;

/**
 * 列車資訊查詢。
 */
final class TrainInfoController extends Controller
{
    /**
     * 車次代碼輸入頁；已帶入車次時直接導向 SEO 網址。
     */
    public function index(): void
    {
        $code = $this->request->query('code', '') ?? '';

        if ($code !== '') {
            $this->redirect('train-info/' . rawurlencode($code));
        }

        $this->render('front/train-info-search', [
            'title'  => '列車資訊',
            'trains' => Train::active()->orderBy('code')->get(),
            'error'  => null,
        ]);
    }

    /**
     * 顯示指定車次的行駛星期、本週日期與各站時刻。
     */
    public function show(string $code): void
    {
        $train = Train::findActiveByCode($code);

        if ($train === null) {
            $this->render('front/train-info-search', [
                'title'  => '列車資訊',
                'trains' => Train::active()->orderBy('code')->get(),
                'error'  => sprintf('查無車次代碼「%s」，請重新輸入', $code),
            ]);

            return;
        }

        $schedule  = ServiceContainer::schedule();
        $timetable = $schedule->timetableOf($train);
        $departure = $schedule->departureMoment($train, new \DateTimeImmutable());

        // 本週日期，每週以星期日為第一天
        $today       = new \DateTimeImmutable('today');
        $weekStart   = $today->modify('-' . (int) $today->format('w') . ' days');
        $weekDates   = [];

        for ($offset = 0; $offset < 7; $offset++) {
            $weekDates[$offset] = $weekStart->modify(sprintf('+%d days', $offset));
        }

        // 把各站的相對分鐘數換算成實際時刻
        $rows = [];

        foreach ($timetable as $entry) {
            $rows[] = [
                'station_name'  => (string) ($entry['stop']->station()?->name ?? ''),
                'arrive_text'   => $entry['arrive_offset'] === null
                    ? '－'
                    : $departure->modify(sprintf('+%d minutes', $entry['arrive_offset']))->format('H:i'),
                'depart_text'   => $entry['depart_offset'] === null
                    ? '－'
                    : $departure->modify(sprintf('+%d minutes', $entry['depart_offset']))->format('H:i'),
                'station_code'  => (string) ($entry['stop']->station()?->code ?? ''),
                'stop_sequence' => (int) $entry['stop']->stop_sequence,
            ];
        }

        $this->render('front/train-info-show', [
            'title'           => sprintf('%s 車次資訊', $train->code),
            'train'           => $train,
            'typeName'        => (string) ($train->type()?->name ?? ''),
            'serviceWeekdays' => $train->serviceWeekdays(),
            'weekdayNames'    => TrainServiceDay::allNames(),
            'weekDates'       => $weekDates,
            'rows'            => $rows,
        ]);
    }
}
