<?php

namespace App\Enums\Admin\Report;

enum ReportPeriod: string
{
    case TODAY = 'today';
    case YESTERDAY = 'yesterday';
    case THIS_WEEK = 'this_week';
    case THIS_MONTH = 'this_month';
    case THIS_QUARTER = 'this_quarter';
    case THIS_YEAR = 'this_year';

    public function label(): string
    {
        return match ($this) {
                self::TODAY => 'Today',
                self::YESTERDAY => 'Yesterday',
                self::THIS_WEEK => 'This Week',
                self::THIS_MONTH => 'This Month',
                self::THIS_QUARTER => 'This Quarter',
                self::THIS_YEAR => 'This Year',
        };
    }
}
