<?php

declare(strict_types=1);

namespace App\Core;

use App\Services\BookingCodeGenerator;
use App\Services\BookingService;
use App\Services\CaptchaService;
use App\Services\ScheduleService;
use App\Services\SeatService;
use App\Services\SmsService;
use App\Services\StatisticsService;
use App\Services\TrainLookupService;
use App\Services\TrainRemovalService;

/**
 * 服務容器。
 *
 * 集中描述各個服務之間的相依關係，控制器只要向容器索取，
 * 不必自己知道某個服務需要哪些協作物件。
 */
final class ServiceContainer
{
    /** @var array<string, object> 已建立的服務實體（每次請求共用同一份） */
    private static array $instances = [];

    public static function schedule(): ScheduleService
    {
        return self::resolve(ScheduleService::class, static fn (): ScheduleService => new ScheduleService());
    }

    public static function seats(): SeatService
    {
        return self::resolve(SeatService::class, static fn (): SeatService => new SeatService());
    }

    public static function sms(): SmsService
    {
        return self::resolve(SmsService::class, static fn (): SmsService => new SmsService(self::schedule()));
    }

    public static function captcha(): CaptchaService
    {
        return self::resolve(CaptchaService::class, static fn (): CaptchaService => new CaptchaService());
    }

    public static function bookingCodes(): BookingCodeGenerator
    {
        return self::resolve(
            BookingCodeGenerator::class,
            static fn (): BookingCodeGenerator => new BookingCodeGenerator()
        );
    }

    public static function bookings(): BookingService
    {
        return self::resolve(BookingService::class, static fn (): BookingService => new BookingService(
            self::schedule(),
            self::seats(),
            self::sms(),
            self::captcha(),
            self::bookingCodes()
        ));
    }

    public static function trainLookup(): TrainLookupService
    {
        return self::resolve(
            TrainLookupService::class,
            static fn (): TrainLookupService => new TrainLookupService(self::schedule(), self::seats())
        );
    }

    public static function statistics(): StatisticsService
    {
        return self::resolve(
            StatisticsService::class,
            static fn (): StatisticsService => new StatisticsService(self::schedule())
        );
    }

    public static function trainRemoval(): TrainRemovalService
    {
        return self::resolve(
            TrainRemovalService::class,
            static fn (): TrainRemovalService => new TrainRemovalService(self::sms())
        );
    }

    /**
     * 取得（必要時先建立）服務實體。
     *
     * @template T of object
     * @param class-string<T> $key
     * @param callable(): T   $factory
     * @return T
     */
    private static function resolve(string $key, callable $factory): object
    {
        if (!isset(self::$instances[$key])) {
            self::$instances[$key] = $factory();
        }

        /** @var T */
        return self::$instances[$key];
    }
}
