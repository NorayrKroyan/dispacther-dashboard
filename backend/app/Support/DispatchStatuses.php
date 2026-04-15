<?php

namespace App\Support;

final class DispatchStatuses
{
    public const ON_DUTY = 'ON DUTY';
    public const OFF_DUTY = 'OFF DUTY';
    public const BREAKDOWN = 'BREAKDOWN';
    public const DAYS_OFF = 'DAYS OFF';

    public static function values(): array
    {
        return [
            self::ON_DUTY,
            self::OFF_DUTY,
            self::BREAKDOWN,
            self::DAYS_OFF,
        ];
    }
}
