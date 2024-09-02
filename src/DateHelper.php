<?php

namespace FL;

class DateHelper
{
    private $value = '';

    public function __construct($datestring = "") {
        if (DateHelper::isValidDate($datestring)) {
            $this->value = new \DateTime($datestring);
        }
    }

    public function modifyDate($intervalSpec) {
        if ($this->value !== "") {
            $interval = new \DateInterval($intervalSpec);
            // Determine if we should add or subtract the interval
            if (strpos($intervalSpec, '-') === 0) {
                $this->value->sub(new DateInterval(ltrim($intervalSpec, '-')));
            } else {
                $this->value->add($interval);
            }
        }
        return $this;
    }

    public function toString() {
        return $this->value->format('Y-m-d');
    }

    public static function isValidDate($datestring) {
        if (empty($datestring)) {
            return false;
        }

        try {
            new DateTime($datestring);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getWeekDaysForYearMonth($year, $month) {
        // the first 28 days of the month contain 20 workdays. So only calculate
        // for the last few days of the month and add 20.
        $year = intval($year);
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