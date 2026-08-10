<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Config;
use App\Models\Booking;

/**
 * 簡訊發送。
 *
 * 本模組不實際串接電信商，改以「網站根目錄/SMS/手機號碼.txt」模擬，
 * 每則簡訊之間以 40 個等號分隔，下一行即為新的一則簡訊。
 */
final class SmsService
{
    /** 每則簡訊之間的分隔線（40 個等號） */
    private const SEPARATOR = '========================================';

    private ScheduleService $scheduleService;

    public function __construct(ScheduleService $scheduleService)
    {
        $this->scheduleService = $scheduleService;
    }

    /**
     * 訂位成功通知。
     *
     * 格式：列車訂位成功。訂票編號：XXXX，MM/DD，XXYY SSSS 車次，N 張票，HH:MM 開，共 ZZZZ 元
     */
    public function sendBookingConfirmed(Booking $booking): void
    {
        $message = sprintf(
            '列車訂位成功。訂票編號：%s，%s，%s %s 車次，%d 張票，%s 開，共 %d 元',
            $booking->booking_code,
            $this->formatTravelDate($booking),
            $this->formatRoute($booking),
            $this->formatTrainCode($booking),
            (int) $booking->ticket_count,
            $this->formatDepartureTime($booking),
            (int) $booking->total_price
        );

        $this->append((string) $booking->phone, $message);
    }

    /**
     * 列車停駛通知。
     *
     * 格式：您所訂的列車已經取消發車。訂票編號：XXXX，MM/DD，XXYY SSSS 車次，請改搭其他列車
     */
    public function sendTrainRemoved(Booking $booking): void
    {
        $message = sprintf(
            '您所訂的列車已經取消發車。訂票編號：%s，%s，%s %s 車次，請改搭其他列車',
            $booking->booking_code,
            $this->formatTravelDate($booking),
            $this->formatRoute($booking),
            $this->formatTrainCode($booking)
        );

        $this->append((string) $booking->phone, $message);
    }

    /**
     * 管理員取消訂票通知。
     *
     * 格式：您的訂票紀錄已被管理員取消。訂票編號：XXXX，MM/DD XXYY，SSSS 車次，取消時間：YYYY/MM/DD
     */
    public function sendCancelledByAdmin(Booking $booking, \DateTimeInterface $cancelledAt): void
    {
        $message = sprintf(
            '您的訂票紀錄已被管理員取消。訂票編號：%s，%s %s，%s 車次，取消時間：%s',
            $booking->booking_code,
            $this->formatTravelDate($booking),
            $this->formatRoute($booking),
            $this->formatTrainCode($booking),
            $cancelledAt->format('Y/m/d')
        );

        $this->append((string) $booking->phone, $message);
    }

    /**
     * 把一則簡訊附加到該手機號碼的檔案末端。
     */
    private function append(string $phone, string $message): void
    {
        $directory = dirname(__DIR__, 2) . '/' . trim((string) Config::get('sms_directory', 'SMS'), '/');

        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        // 手機號碼只保留數字，避免有人用 ../ 之類的內容操控檔案路徑
        $safePhone = preg_replace('/\D+/', '', $phone) ?? '';

        if ($safePhone === '') {
            return;
        }

        file_put_contents(
            $directory . '/' . $safePhone . '.txt',
            self::SEPARATOR . "\n" . $message . "\n",
            FILE_APPEND | LOCK_EX
        );
    }

    /**
     * 乘車日期，格式 MM/DD。
     */
    private function formatTravelDate(Booking $booking): string
    {
        return (new \DateTimeImmutable((string) $booking->travel_date))->format('m/d');
    }

    /**
     * 起迄站，格式為兩站的中文名稱相接（XXYY）。
     */
    private function formatRoute(Booking $booking): string
    {
        return ($booking->fromStation()?->name ?? '') . ($booking->toStation()?->name ?? '');
    }

    /**
     * 車次代碼。
     */
    private function formatTrainCode(Booking $booking): string
    {
        return (string) ($booking->train()?->code ?? '');
    }

    /**
     * 發車時間，24 小時制的 HH:MM。
     */
    private function formatDepartureTime(Booking $booking): string
    {
        return (new \DateTimeImmutable((string) $booking->depart_at))->format('H:i');
    }
}
