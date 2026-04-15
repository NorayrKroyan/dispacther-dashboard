<?php

namespace App\Support;

final class DispatchEvents
{
    public const EMPTY = '';
    public const ENTER_JOB_SITE = 'Enter Job Site';
    public const EXIT_JOB_SITE = 'Exit Job Site';
    public const ENTER_PULL_POINT = 'Enter Pull Point';
    public const EXIT_PULL_POINT = 'Exit Pull Point';

    public static function values(): array
    {
        return [
            self::EMPTY,
            self::ENTER_JOB_SITE,
            self::EXIT_JOB_SITE,
            self::ENTER_PULL_POINT,
            self::EXIT_PULL_POINT,
        ];
    }
}
