<?php

namespace FL;

class DateHelper
{
    public static function getWeekDaysForYearMonth($year, $month) {
        // the first 28 days of the month contain 20 workdays. So only calculate
        // for the last few days of the month and add 20.
        $lastday = date("t", mktime(0, 0, 0, $month, 1, $year));
        $weekdays = 0;
        for ($day = 29; $day <= $lastday; $day++) {
            $wd = date("w", mktime(0, 0, 0, $month, $day, $year));
            if ($wd > 0 && $wd < 6)
                $weekdays++;
        }
        return $weekdays + 20;
    }

}