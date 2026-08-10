<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Paginator;
use App\Core\ServiceContainer;
use App\Core\Session;
use App\Models\Booking;
use App\Models\Station;
use App\Models\Train;

/**
 * 前台訂票與訂票查詢。
 */
final class BookingController extends Controller
{
    /**
     * 訂票頁面。
     *
     * 車次、起訖站與日期可由車次查詢或列車資訊頁以查詢字串帶入。
     */
    public function create(): void
    {
        $captcha  = ServiceContainer::captcha();
        $question = $captcha->currentQuestion();

        $this->render('front/booking-create', [
            'title'           => '預訂車票',
            'stations'        => Station::allOrdered(),
            'trains'          => Train::active()->orderBy('code')->get(),
            'errors'          => Session::pullFlash('errors', []),
            'old'             => Session::pullFlash('old', []),
            'prefill'         => [
                'train_code'   => $this->request->query('train_code', '') ?? '',
                'from_station' => $this->request->query('from_station', '') ?? '',
                'to_station'   => $this->request->query('to_station', '') ?? '',
                'travel_date'  => $this->request->query('travel_date', '') ?? '',
            ],
            'captchaQuestion' => $question,
            // 驗證通過後在同一個 Session 內維持有效，直到訂票成功才失效，
            // 使用者不會因為其他欄位填錯就得重新作答
            'captchaPassed'   => $captcha->hasPassed(),
            'today'           => (new \DateTimeImmutable())->format('Y-m-d'),
        ]);
    }

    /**
     * 送出訂票。
     */
    public function store(): void
    {
        $input = [
            'phone'        => $this->request->input('phone', '') ?? '',
            'train_code'   => $this->request->input('train_code', '') ?? '',
            'from_station' => $this->request->input('from_station', '') ?? '',
            'to_station'   => $this->request->input('to_station', '') ?? '',
            'travel_date'  => $this->request->input('travel_date', '') ?? '',
            'ticket_count' => $this->request->input('ticket_count', '') ?? '',
        ];

        $result = ServiceContainer::bookings()->book($input, new \DateTimeImmutable());

        // 有任何錯誤都帶著訊息與原輸入值回到訂票頁面
        if ($result['booking'] === null) {
            $this->redirectWithErrors('booking', $result['errors'], $input);
        }

        $this->redirect('booking/success/' . rawurlencode((string) $result['booking']->booking_code));
    }

    /**
     * 訂票成功頁。
     */
    public function success(string $code): void
    {
        $booking = Booking::findByCode($code);

        if ($booking === null) {
            $this->redirectWithErrors('bookings', ['查無此訂票紀錄']);
        }

        $this->render('front/booking-success', [
            'title'   => '訂票成功',
            'booking' => $booking,
        ]);
    }

    /**
     * 以 JSON 回傳指定車次的行經車站，供訂票頁的下拉選單動態更新。
     */
    public function stops(string $code): void
    {
        $train = Train::findActiveByCode($code);

        if ($train === null) {
            $this->json(['success' => false, 'stops' => []], 404);
        }

        $stops = [];

        foreach ($train->stops() as $stop) {
            $station = $stop->station();

            if ($station === null) {
                continue;
            }

            $stops[] = [
                'sequence' => (int) $stop->stop_sequence,
                'code'     => (string) $station->code,
                'name'     => (string) $station->name,
            ];
        }

        $this->json(['success' => true, 'stops' => $stops]);
    }

    /**
     * 訂票查詢：可用訂票編號或手機號碼查詢，每頁最多 3 筆。
     */
    public function search(): void
    {
        $keyword    = $this->request->query('keyword', '') ?? '';
        $page       = max(1, (int) ($this->request->query('page', '1') ?? '1'));
        $paginator  = null;
        $hasQueried = $keyword !== '';

        if ($hasQueried) {
            $perPage = (int) \App\Core\Config::get('pagination.front_bookings', 3);

            // 訂票編號與手機號碼擇一符合即可
            $countQuery = Booking::query();
            $total      = $this->matchKeyword($countQuery, $keyword)->count();

            $paginator = Paginator::make(
                $total,
                $perPage,
                $page,
                function (int $limit, int $offset) use ($keyword): array {
                    return $this->matchKeyword(Booking::query(), $keyword)
                        ->orderBy('created_at', 'DESC')
                        ->orderBy('id', 'DESC')
                        ->limit($limit)
                        ->offset($offset)
                        ->get();
                },
                $this->view->url('bookings'),
                ['keyword' => $keyword]
            );
        }

        $this->render('front/booking-search', [
            'title'      => '訂票查詢',
            'keyword'    => $keyword,
            'hasQueried' => $hasQueried,
            'paginator'  => $paginator,
            'errors'     => Session::pullFlash('errors', []),
            'notice'     => Session::pullFlash('notice'),
            'now'        => new \DateTimeImmutable(),
        ]);
    }

    /**
     * 乘客自行取消訂票。
     */
    public function cancel(string $code): void
    {
        $booking = Booking::findByCode($code);
        $keyword = $this->request->input('keyword', '') ?? '';
        $backUrl = 'bookings?' . http_build_query(['keyword' => $keyword]);

        if ($booking === null) {
            $this->redirectWithErrors($backUrl, ['查無此訂票紀錄']);
        }

        if ($booking->isCancelled()) {
            $this->redirectWithErrors($backUrl, ['此訂票紀錄已經取消過了']);
        }

        ServiceContainer::bookings()->cancel($booking, Booking::CANCELLED_BY_PASSENGER, new \DateTimeImmutable());

        Session::flash('notice', sprintf('訂票編號 %s 已取消', $booking->booking_code));
        $this->redirect($backUrl);
    }

    /**
     * 讓查詢條件同時比對訂票編號與手機號碼。
     */
    private function matchKeyword(\App\Core\QueryBuilder $query, string $keyword): \App\Core\QueryBuilder
    {
        // 只由數字組成時視為手機號碼，否則視為訂票編號
        return ctype_digit($keyword)
            ? $query->where('phone', $keyword)
            : $query->where('booking_code', $keyword);
    }
}
