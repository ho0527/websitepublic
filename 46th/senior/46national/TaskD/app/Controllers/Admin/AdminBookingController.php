<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Config;
use App\Core\Controller;
use App\Core\Paginator;
use App\Core\QueryBuilder;
use App\Core\ServiceContainer;
use App\Core\Session;
use App\Models\Booking;
use App\Models\Station;
use App\Models\Train;

/**
 * 後台訂票紀錄查詢。
 */
final class AdminBookingController extends Controller
{
    /**
     * 依選定條件查詢訂票紀錄，每頁最多 5 筆。
     *
     * 未填寫的欄位不會被當成過濾條件。
     */
    public function index(): void
    {
        $this->requireAdmin();

        $filters = [
            'travel_date'  => $this->request->query('travel_date', '') ?? '',
            'train_code'   => $this->request->query('train_code', '') ?? '',
            'phone'        => $this->request->query('phone', '') ?? '',
            'booking_code' => $this->request->query('booking_code', '') ?? '',
            'from_station' => $this->request->query('from_station', '') ?? '',
            'to_station'   => $this->request->query('to_station', '') ?? '',
        ];

        $page    = max(1, (int) ($this->request->query('page', '1') ?? '1'));
        $perPage = (int) Config::get('pagination.admin_bookings', 5);
        $total   = $this->applyFilters(Booking::query(), $filters)->count();

        $paginator = Paginator::make(
            $total,
            $perPage,
            $page,
            function (int $limit, int $offset) use ($filters): array {
                return $this->applyFilters(Booking::query(), $filters)
                    ->orderBy('depart_at', 'DESC')
                    ->orderBy('id', 'DESC')
                    ->limit($limit)
                    ->offset($offset)
                    ->get();
            },
            $this->view->url('admin/bookings'),
            $filters
        );

        $this->renderAdmin('admin/bookings/index', [
            'title'     => '訂票紀錄查詢',
            'filters'   => $filters,
            'paginator' => $paginator,
            'stations'  => Station::allOrdered(),
            'trains'    => Train::query()->orderBy('code')->get(),
            'errors'    => Session::pullFlash('errors', []),
            'notice'    => Session::pullFlash('notice'),
            'now'       => new \DateTimeImmutable(),
        ]);
    }

    /**
     * 管理員取消尚未發車的訂票。
     */
    public function cancel(string $code): void
    {
        $this->requireAdmin();

        $booking  = Booking::findByCode($code);
        $filters  = $this->request->allQuery();
        $backUrl  = 'admin/bookings?' . http_build_query($filters);
        $now      = new \DateTimeImmutable();

        if ($booking === null) {
            $this->redirectWithErrors($backUrl, ['查無此訂票紀錄']);
        }

        if (!$booking->isCancellable($now)) {
            $this->redirectWithErrors($backUrl, ['此訂票已取消或列車已發車，無法取消']);
        }

        ServiceContainer::bookings()->cancel($booking, Booking::CANCELLED_BY_ADMIN, $now);

        Session::flash('notice', sprintf('已取消訂票編號 %s，並發送簡訊通知乘客', $booking->booking_code));
        $this->redirect($backUrl);
    }

    /**
     * 套用查詢條件；空白欄位不列入過濾。
     *
     * @param array<string, string> $filters
     */
    private function applyFilters(QueryBuilder $query, array $filters): QueryBuilder
    {
        if ($filters['travel_date'] !== '') {
            $query->where('travel_date', $filters['travel_date']);
        }

        if ($filters['train_code'] !== '') {
            $train = Train::where('code', $filters['train_code'])->first();
            // 查無此車次時讓結果為空，而不是忽略這個條件
            $query->where('train_id', $train?->id() ?? 0);
        }

        if ($filters['phone'] !== '') {
            $query->where('phone', $filters['phone']);
        }

        if ($filters['booking_code'] !== '') {
            $query->where('booking_code', $filters['booking_code']);
        }

        if ($filters['from_station'] !== '') {
            $query->where('from_station_id', Station::findByCode($filters['from_station'])?->id() ?? 0);
        }

        if ($filters['to_station'] !== '') {
            $query->where('to_station_id', Station::findByCode($filters['to_station'])?->id() ?? 0);
        }

        return $query;
    }
}
