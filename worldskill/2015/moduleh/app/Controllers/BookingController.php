<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Booking;
use App\Models\BookingContact;
use App\Models\Reservation;
use App\Services\BookingService;

/**
 * 訂位流程控制器：
 *   contact       -> 聯絡人資料與賓客規範（必須勾選同意才能繼續）
 *   individual    -> 個人訂位（勾選一個或多個場次）
 *   group         -> 團體訂位（分頁顯示四個競賽日）
 *   confirmation  -> 送出確認頁
 */
class BookingController extends Controller
{
    /** @var string session 中暫存聯絡人資料的鍵名 */
    private const CONTACT_KEY = 'booking_contact';

    /** @var string session 中暫存最後一筆訂位 id 的鍵名 */
    private const BOOKING_KEY = 'last_booking_id';

    /**
     * 步驟一：聯絡人資料 + 賓客規範
     */
    public function contact(): string
    {
        $data   = $this->request->session(self::CONTACT_KEY, []);
        $errors = [];

        if ($this->request->isPost()) {
            $data = [
                'name'         => $this->request->post('name'),
                'organization' => $this->request->post('organization'),
                'email'        => $this->request->post('email'),
                'phone'        => $this->request->post('phone'),
                'country'      => $this->request->post('country'),
                'agree'        => $this->request->hasPost('agree'),
            ];

            $errors = BookingContact::validate($data);

            if ($errors === []) {
                $this->request->setSession(self::CONTACT_KEY, $data);

                // 依按下的按鈕決定下一步
                $this->redirect(
                    $this->request->hasPost('agree-group') ? 'booking/group' : 'booking/individual'
                );
            }
        }

        return $this->render('contact', [
            'pageTitle'  => 'Booking contact details and guest regulations',
            'breadcrumb' => ['Booking', 'Contact details'],
            'data'       => $data,
            'errors'     => $errors,
        ]);
    }

    /**
     * 步驟二 A：個人訂位
     */
    public function individual(): string
    {
        $contact = $this->requireContact();
        $service = new BookingService();
        $errors  = [];

        if ($this->request->isPost()) {
            $selected = $service->parseIndividualSelection($_POST);

            if ($selected === []) {
                $errors[] = 'Please select at least one seating.';
            } else {
                $booking = $service->storeIndividual($contact, $selected);
                $this->finishBooking($booking['id']);
            }
        }

        return $this->render('individual', [
            'pageTitle'    => 'Booking request',
            'breadcrumb'   => ['Booking', 'Individual'],
            'contact'      => $contact,
            'days'         => $service->days(),
            'seatings'     => $service->seatings(),
            'availability' => $service->availability(),
            'errors'       => $errors,
        ]);
    }

    /**
     * 步驟二 B：團體訂位
     */
    public function group(): string
    {
        $contact = $this->requireContact();
        $service = new BookingService();
        $errors  = [];
        $guests  = [];

        if ($this->request->isPost()) {
            $guests = $service->parseGroupInput($_POST);
            $errors = $service->validateGroup($guests);

            $hasGuest = false;

            foreach ($guests as $rows) {
                foreach ($rows as $row) {
                    if ($row['country'] !== '') {
                        $hasGuest = true;
                        break 2;
                    }
                }
            }

            if ($errors === [] && !$hasGuest) {
                $errors[] = 'Please enter at least one guest with a country selected.';
            }

            if ($errors === []) {
                $booking = $service->storeGroup($contact, $guests);
                $this->finishBooking($booking['id']);
            }
        }

        return $this->render('group', [
            'pageTitle'    => 'Booking request',
            'breadcrumb'   => ['Booking', 'Group'],
            'contact'      => $contact,
            'days'         => $service->days(),
            'seatings'     => $service->seatings(),
            'availability' => $service->availability(),
            'guests'       => $guests,
            'errors'       => $errors,
        ]);
    }

    /**
     * 步驟三：送出確認頁（資料由資料庫讀回，確保顯示的是真正存下來的內容）
     */
    public function confirmation(): string
    {
        $bookingId = (int) $this->request->session(self::BOOKING_KEY, 0);

        if ($bookingId <= 0) {
            $this->redirect('booking/contact');
        }

        $booking      = (new Booking())->findWithContact($bookingId);
        $reservations = (new Reservation())->forBooking($bookingId);

        if ($booking === null) {
            $this->redirect('booking/contact');
        }

        // 依「競賽日 + 場次」分組顯示
        $groups = [];

        foreach ($reservations as $reservation) {
            $key = sprintf(
                '%s - %s, %s %s - %s',
                $reservation['day_code'],
                date('d.m.Y', strtotime($reservation['day_date'])),
                $reservation['module_name'],
                substr($reservation['start_time'], 0, 5),
                substr($reservation['end_time'], 0, 5)
            );

            $groups[$key][] = $reservation;
        }

        return $this->render('confirmation', [
            'pageTitle'  => 'Submission confirmation',
            'breadcrumb' => ['Booking', 'Confirmation'],
            'booking'    => $booking,
            'groups'     => $groups,
        ]);
    }

    /**
     * 取出 session 中的聯絡人資料；若不存在（尚未同意規範）則導回第一步
     */
    private function requireContact(): array
    {
        $contact = $this->request->session(self::CONTACT_KEY);

        if (!is_array($contact) || empty($contact['agree'])) {
            $this->redirect('booking/contact');
        }

        return $contact;
    }

    /**
     * 訂位寫入成功後：清掉暫存的聯絡人資料並導向確認頁
     */
    private function finishBooking(int $bookingId): void
    {
        $this->request->setSession(self::BOOKING_KEY, $bookingId);
        $this->request->forgetSession(self::CONTACT_KEY);
        $this->redirect('booking/confirmation');
    }
}
