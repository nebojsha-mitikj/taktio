<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Support\Carbon;

trait ResolvesPlanDate
{
    private const MODE_PAST = 'past';

    private const MODE_CURRENT = 'current';

    private const MODE_FUTURE = 'future';

    private function abortInvalidDate(int $year, int $month): void
    {
        abort_if($year < 2025 || $year > 2099, 404);
        abort_if($month < 1 || $month > 12, 404);
    }

    /**
     * @return self::MODE_PAST|self::MODE_CURRENT|self::MODE_FUTURE
     */
    private function resolveMode(int $year, int $month): string
    {
        $now = Carbon::today();
        $planned = $year * 100 + $month;
        $current = $now->year * 100 + $now->month;

        return match (true) {
            $planned < $current => self::MODE_PAST,
            $planned === $current => self::MODE_CURRENT,
            default => self::MODE_FUTURE,
        };
    }

    private function isPast(int $year, int $month): bool
    {
        return $this->resolveMode($year, $month) === self::MODE_PAST;
    }
}
