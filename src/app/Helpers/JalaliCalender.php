<?php

namespace App\Helpers;

use App\Exceptions\Http\FatalErrorException;
use Carbon\Carbon;
use Exception;
use InvalidArgumentException;

/**
 * Class JalaliCalender
 * @package App\Helpers
 */
class JalaliCalender
{
    public static function getJalaliString(Carbon $carbon): string
    {
        return self::toJalali($carbon->year, $carbon->month, $carbon->day);
    }

    /**
     * @param $gy
     * @param $gm
     * @param $gd
     * @return string
     */
    public static function toJalali($gy, $gm, $gd)
    {
        return self::d2j(self::g2d($gy, $gm, $gd));
    }

    /**
     * @param $jy
     * @return array
     */
    public static function jalaliCal($jy)
    {
        $breaks = [
            -61,
            9,
            38,
            199,
            426,
            686,
            756,
            818,
            1111,
            1181,
            1210,
            1635,
            2060,
            2097,
            2192,
            2262,
            2324,
            2394,
            2456,
            3178
        ];

        $breaksCount = count($breaks);

        $gy = $jy + 621;
        $leapJ = -14;
        $jp = $breaks[0];

        if ($jy < $jp || $jy >= $breaks[$breaksCount - 1]) {
            throw new InvalidArgumentException('Invalid Jalali year : ' . $jy);
        }

        $jump = 0;

        for ($i = 1; $i < $breaksCount; ++$i) {
            $jm = $breaks[$i];
            $jump = $jm - $jp;

            if ($jy < $jm) {
                break;
            }

            $leapJ += self::div($jump, 33) * 8 + self::div(self::mod($jump, 33), 4);

            $jp = $jm;
        }

        $n = $jy - $jp;

        $leapJ += self::div($n, 33) * 8 + self::div(self::mod($n, 33) + 3, 4);

        if (self::mod($jump, 33) === 4 && $jump - $n === 4) {
            ++$leapJ;
        }

        $leapG = self::div($gy, 4) - self::div((self::div($gy, 100) + 1) * 3, 4) - 150;

        $march = 20 + $leapJ - $leapG;

        if ($jump - $n < 6) {
            $n = $n - $jump + self::div($jump + 4, 33) * 33;
        }

        $leap = self::mod(self::mod($n + 1, 33) - 1, 4);

        if ($leap === -1) {
            $leap = 4;
        }

        return [
            'leap'  => $leap,
            'gy'    => $gy,
            'march' => $march
        ];
    }

    /**
     * @param $a
     * @param $b
     * @return bool
     */
    public static function div($a, $b)
    {
        return ~~($a / $b);
    }

    /**
     * @param $a
     * @param $b
     * @return float|int
     */
    public static function mod($a, $b)
    {
        return $a - ~~($a / $b) * $b;
    }

    /**
     * @param $jdn
     * @return array
     */
    public static function d2g($jdn)
    {
        $j = 4 * $jdn + 139361631;
        $j += self::div(self::div(4 * $jdn + 183187720, 146097) * 3, 4) * 4 - 3908;
        $i = self::div(self::mod($j, 1461), 4) * 5 + 308;

        $gd = self::div(self::mod($i, 153), 5) + 1;
        $gm = self::mod(self::div($i, 153), 12) + 1;
        $gy = self::div($j, 1461) - 100100 + self::div(8 - $gm, 6);

        return [$gy, $gm, $gd];
    }

    /**
     * @param $gy
     * @param $gm
     * @param $gd
     * @return bool|int
     */
    public static function g2d($gy, $gm, $gd)
    {
        return (
                self::div(($gy + self::div($gm - 8, 6) + 100100) * 1461, 4)
                + self::div(153 * self::mod($gm + 9, 12) + 2, 5)
                + $gd - 34840408
            ) - self::div(self::div($gy + 100100 + self::div($gm - 8, 6), 100) * 3, 4) + 752;
    }

    /**
     * @param $jdn
     * @return string
     */
    public static function d2j($jdn)
    {
        $gy = self::d2g($jdn)[0];
        $jy = $gy - 621;
        $jCal = self::jalaliCal($jy);
        $jdn1f = self::g2d($gy, 3, $jCal['march']);

        $k = $jdn - $jdn1f;

        if ($k >= 0) {
            if ($k <= 185) {
                $jm = 1 + self::div($k, 31);
                $jd = self::mod($k, 31) + 1;

                return self::appendZero([$jy, $jm, $jd]);
            }

            $k -= 186;
        } else {
            --$jy;
            $k += 179;

            if ($jCal['leap'] === 1) {
                ++$k;
            }
        }

        $jm = 7 + self::div($k, 30);
        $jd = self::mod($k, 30) + 1;

        return self::appendZero([$jy, $jm, $jd]);
    }

    /**
     * @param $date
     * @return string
     */
    public static function appendZero($date)
    {
        if ($date[1] < 10) {
            $date[1] = '0' . $date[1];
        }
        if ($date[2] < 10) {
            $date[2] = '0' . $date[2];
        }

        return implode('/', $date);
    }

    /**
     * @param $date
     * @return string
     * @throws Exception
     */
    public static function timeElapsed($date)
    {
        Carbon::setLocale('fa');

        $date = new Carbon($date);

        return $date->diffForHumans();
    }

    /**
     * Converts a Jalaali date to Gregorian.
     *
     * @param integer $jy Jalaali Year
     * @param integer $jm Jalaali Month
     * @param integer $jd Jalaali Day
     *
     * @return array The converted Gregorian date
     */
    public static function toGregorian($jy, $jm, $jd)
    {
        return self::d2g(self::j2d($jy, $jm, $jd));
    }

    /**
     * Converts a date of the Jalaali calendar to the Julian Day Number.
     *
     * @param integer $jy Jalaali Year (1 to 3100)
     * @param integer $jm Jalaali Month (1 to 12)
     * @param integer $jd Jalaali Day (1 to 29/31)
     *
     * @return $jdn Julian Day Number
     */
    public static function j2d($jy, $jm, $jd)
    {
        $result = self::jalaliCal($jy);
        return self::g2d($result['gy'], 3, $result['march']) + ($jm - 1) * 31 - self::div($jm, 7) * ($jm - 7) + $jd - 1;
    }

    /**
     * Checks whether a Jalaali date is valid or not.
     *
     * @param integer $jy Jalaali Year.
     * @param integer $jm Jalaali Month.
     * @param integer $jd Jalaali Day.
     *
     * @return boolean is date valid?
     */
    public static function isValidJalaaliDate($jy, $jm, $jd)
    {
        $yearIsValid = ($jy >= -61 && $jy <= 3177);
        $monthIsValid = ($jm >= 1 && $jm <= 12);
        $dayIsValid = ($jd >= 1 && $jd <= self::jalaaliMonthLength($jy, $jm));

        return $yearIsValid && $monthIsValid && $dayIsValid;
    }

    /**
     * Number of days in a given month in Jalaali year.
     *
     * @param integer $jy Jalaali Year.
     * @param integer $jm Jalaali Month.
     *
     * @return integer
     */
    public static function jalaaliMonthLength($jy, $jm)
    {
        if ($jm <= 6) return 31;
        if ($jm <= 11) return 30;
        if (self::isLeapJalaaliYear($jy)) return 30;

        return 29;
    }

    /**
     * Checks whether this is a leap year or not.
     *
     * @param integer $jy Jalaali Year.
     *
     * @return boolean is leap year?
     */
    public static function isLeapJalaaliYear($jy)
    {
        $result = self::jalaliCal($jy);
        return $result['leap'] == 0;
    }

    /**
     * @param null $jalali_year
     * @param null $jalali_month
     * @param null $jalali_day
     * @param string|null $cycle
     * @param bool|null $last_cycle
     * @param int|null $custom_days
     * @return array|\Illuminate\Support\Carbon[]
     */
    public static function getRange($jalali_year = null, $jalali_month = null, $jalali_day = null, ?string $cycle = null, ?bool $last_cycle = null, ?int $custom_days = null): array
    {
        $cycle = $cycle ?? 'monthly';

        $last_cycle = !is_null($last_cycle) ? $last_cycle : true;

        $date_time = \Illuminate\Support\Carbon::now();

        [$j_year, $j_month, $j_day] = explode('/', JalaliCalender::toJalali($date_time->year, $date_time->month, $date_time->day));

        $jalali_year_from = $jalali_year > 0 ? $jalali_year : $j_year;

        $jalali_month_from = $jalali_month > 0 ? $jalali_month : $j_month;

        $jalali_day_from = $jalali_day > 0 ? $jalali_day : $j_day;

        switch ($cycle) {
            case 'monthly':
                $jalali_day_from = 1;
                if ($last_cycle == true) {
                    if ($jalali_month_from > 1) {
                        $jalali_month_from--;
                    } else {
                        $jalali_year_from--;
                        $jalali_month_from = 12;
                    }
                }
                $date_from = self::makeCarbonByJalali(
                    $jalali_year_from,
                    $jalali_month_from,
                    $jalali_day_from,
                );
                $date_to = self::makeCarbonByJalali(
                    $jalali_year_from,
                    $jalali_month_from,
                    JalaliCalender::jalaaliMonthLength($jalali_year_from, $jalali_month_from),
                );
                break;
            case 'weekly':
                $date_time = self::makeCarbonByJalali(
                    $jalali_year_from,
                    $jalali_month_from,
                    $jalali_day_from,
                );

                $date_from = $date_time->clone()->subDays($date_time->dayOfWeek + ($last_cycle ? 8 : 1));
                $date_to = $date_from->clone()->addDays(6);
                break;
            default:
                $date_from = self::makeCarbonByJalali(
                    $jalali_year_from,
                    $jalali_month_from,
                    $jalali_day_from,
                );
                $date_to = $date_from->clone()->addDays(!is_null($custom_days) && $custom_days > -1 ? $custom_days : 1);
        }

        return [
            $date_from->setHour(0)->setMinute(0)->setSecond(0),
            $date_to->setHour(23)->setMinute(59)->setSecond(59)
        ];
    }

    /**
     * Gets Illuminate Carbon instance by given jalali date
     *
     * @param $jalali_year
     * @param $jalali_month
     * @param $jalali_day
     * @return \Illuminate\Support\Carbon
     */
    public static function makeCarbonByJalali($jalali_year, $jalali_month, $jalali_day): \Illuminate\Support\Carbon
    {
        if (!JalaliCalender::isValidJalaaliDate($jalali_year, $jalali_month, $jalali_day)) {
            jdd($jalali_year, $jalali_month, $jalali_day);
            throw new FatalErrorException('Invalid date');
        }

        $date = JalaliCalender::toGregorian(
            $jalali_year,
            $jalali_month,
            $jalali_day
        );

        return \Illuminate\Support\Carbon::createFromDate(...$date);
    }

    /**
     * Converts Illuminate Carbon object to jalali date
     *
     * @param \Illuminate\Support\Carbon $carbon
     * @return string
     */
    public static function carbonToJalali(\Illuminate\Support\Carbon $carbon): string
    {
        return self::d2j(self::g2d($carbon->year, $carbon->month, $carbon->day));
    }
}
