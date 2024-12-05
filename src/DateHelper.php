<?php

namespace FL;

class DateTimeToolFormat {

    const SUNDAY = 0;
    const MONDAY = 1;
    const TUESDAY = 2;
    const WEDNESDAY = 3;
    const THURSDAY = 4;
    const FRIDAY = 5;
    const SATURDAY = 6;
    // full date formats
    const DEFAULT = "Y-m-d";
    const YMD = "Y-m-d";
    const YMDHMS = "Y-m-d H:i:s";
    const LONG = "l j F Y \a\t g:i";
    // year formats
    const YEAR = "Y";
    // month formats
    const MONTH = "m";
    const MONTHNOLEADINGZEROES = "n";
    const MONTHNAME = "F";
    // week formats
    const WEEKNUM = "W";
    // day formats
    const DAY = "d";
    const DAYNOLEADINGZEROES = "j";
    const DAYNAME = "l";
    const DAYNUM = "w";  // 0 = sunday, 6 = saturday
    const DAYNUMISO = "N"; // 1= monday, 7 = sunday
    const DAYSUFFIX = "S"; // st, nd, rd or th. Works well with j
    const DAYOFTHEYEAR = "z"; // 0 through 365
    // hour formats
    const HOUR = "H"; // 00 - 23
    const HOURNOLEADINGZEROES = "G"; // 0 - 23
    const HOUR24 = "H"; // 00 - 23
    const HOUR24NOLEADINGZEROES = "G"; // 0 - 23
    const HOUR12 = "h"; // 01 - 12
    const HOUR12NOLEADINGZEROES = "g"; // 1 - 12
    // minute formats
    const MINUTE = "i"; // 00 - 59
    // second formats
    const SECOND = "s"; // 00 - 59
    // microsecond or millisecond format;
    const MICROSECOND = "u";
    const MILLISECOND = "v";
    // AM OR PM
    const AMORPM = "A"; // AM or PM
    const AMORPMLOWERCASE = "a"; // am or pm

}

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

    public static function minutesToHours($minutes) {
        return date('H:i', mktime(0, $minutes));
    }

    public function isWeekDay() {
        if ($this->value->format(DateTimeToolFormat::DAYNUM) == DateTimeToolFormat::SATURDAY || $this->value->format(DateTimeToolFormat::DAYNUM) == DateTimeToolFormat::SUNDAY) {
            return false;
        }
        return true;
    }
    public function isWeekend() {
        if ($this->isWeekDay()) {
            return false;
        }
        return true;
    }
    public function getDayNumInWeek() {
        // 0 = Sunday, 1 = Monday, .... , 6 = Saturday
        return $this->toString(DateTimeToolFormat::DAYNUM);
    }

    public function getDayInWeek() {
        // Monday through Sunday
        return $this->toString(DateTimeToolFormat::DAYNAME);
    }

    public function getWeekNum() {
        return $this->toString(DateTimeToolFormat::WEEKNUM);
    }

    public function getHour() {
        return $this->toString(DateTimeToolFormat::HOURNOLEADINGZEROES);
    }

    public function getMinute() {
        return $this->toString(DateTimeToolFormat::MINUTE);
    }

    public function getSecond() {
        return $this->toString(DateTimeToolFormat::SECOND);
    }

}