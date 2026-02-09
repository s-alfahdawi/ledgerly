<?php

namespace App\Support;

use Carbon\Carbon;

/**
 * ReportPeriod
 * 
 * Value object for handling date ranges in reports with timezone awareness.
 * Centralizes date logic to avoid bugs and keep code readable.
 */
class ReportPeriod
{
    public function __construct(
        public readonly Carbon $startDate,
        public readonly Carbon $endDate,
        public readonly string $timezone = 'UTC'
    ) {
    }

    /**
     * Create a ReportPeriod for a specific month/year.
     */
    public static function forMonth(int $month, int $year, string $timezone = 'UTC'): self
    {
        $startDate = Carbon::create($year, $month, 1, 0, 0, 0, $timezone);
        $endDate = $startDate->copy()->endOfMonth();

        return new self($startDate, $endDate, $timezone);
    }

    /**
     * Create a ReportPeriod from date strings.
     */
    public static function fromDates(string $startDate, string $endDate, string $timezone = 'UTC'): self
    {
        return new self(
            Carbon::parse($startDate, $timezone)->startOfDay(),
            Carbon::parse($endDate, $timezone)->endOfDay(),
            $timezone
        );
    }

    /**
     * Create a ReportPeriod for current month.
     */
    public static function currentMonth(string $timezone = 'UTC'): self
    {
        $now = Carbon::now($timezone);
        return self::forMonth($now->month, $now->year, $timezone);
    }

    /**
     * Get start date as formatted string for database queries.
     * Converts to UTC since the database stores datetimes in UTC.
     */
    public function startDateString(): string
    {
        return $this->startDate->copy()->utc()->format('Y-m-d H:i:s');
    }

    /**
     * Get end date as formatted string for database queries.
     * Converts to UTC since the database stores datetimes in UTC.
     */
    public function endDateString(): string
    {
        return $this->endDate->copy()->utc()->format('Y-m-d H:i:s');
    }

    /**
     * Get start date as date-only string.
     */
    public function startDateOnly(): string
    {
        return $this->startDate->format('Y-m-d');
    }

    /**
     * Get end date as date-only string.
     */
    public function endDateOnly(): string
    {
        return $this->endDate->format('Y-m-d');
    }
}
