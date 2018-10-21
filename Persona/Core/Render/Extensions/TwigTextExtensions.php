<?php

namespace Core\Render\Extensions;

use Core\Router\Router;


class TwigTextExtensions extends \Twig_Extension
{

    const MINUTE = 60;
    const HOUR = 3600;
    const DAY = 86400;
    const WEEK = 604800;
    const MONTH = 2628000;
    const YEAR = 31536000;

    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('excerpt', [$this, 'excerpt']),
            new \Twig_SimpleFilter('ago', [$this, 'ago'], ['needs_environment' => true])
        ];
    }
    /**
     * cut string
     *
     * @param string $content
     * @param integer $length
     * @param string $end
     * @return string
     */
    public function excerpt(string $content, int $length = 100, string $end = "...") : string
    {
        if (mb_strlen($content) > $length) {
            $excerpt = mb_substr($content, 0, $length);
            $lastspace = mb_strrpos($excerpt, ' ');
            return mb_substr($content, 0, $lastspace) . $end;
        }
        return $content;
    }

    public function ago(\Twig_Environment $env, string $value, string $format = 'd-m-Y H:i') : string
    {
        $dateTime = twig_date_converter($env, $value);

        $reference = new \DateTime(null, new \DateTimeZone($dateTime->getTimezone()->getName()));
        $difference = $reference->format('U') - $dateTime->format('U');
        $absDiff = abs($difference);
            // Get the date corresponding to the $dateTime
        $date = $dateTime->format($format);
            // Throw exception if the difference is NaN
        if (is_nan($difference)) {
            throw new Exception('The difference between the DateTimes is NaN.');
        }
            // Today
        if ($reference->format('Y/m/d') == $dateTime->format('Y/m/d')) {
            if (0 <= $difference && $absDiff < self::MINUTE) {
                return 'Moments ago';
            } elseif ($difference < 0 && $absDiff < self::MINUTE) {
                return 'Seconds from now';
            } elseif ($absDiff < self::HOUR) {
                return self::prettyFormat($difference / self::MINUTE, 'minute');
            } else {
                return self::prettyFormat($difference / self::HOUR, 'hour');
            }
        }
        $yesterday = clone $reference;
        $yesterday->modify('- 1 day');
        $tomorrow = clone $reference;
        $tomorrow->modify('+ 1 day');
        if ($yesterday->format('Y/m/d') == $date) {
            return 'Yesterday';
        } else if ($tomorrow->format('Y/m/d') == $date) {
            return 'Tomorrow';
        } else if ($absDiff / self::DAY <= 7) {
            return self::prettyFormat($difference / self::DAY, 'day');
        } else if ($absDiff / self::WEEK <= 5) {
            return self::prettyFormat($difference / self::WEEK, 'week');
        } else if ($absDiff / self::MONTH < 12) {
            return self::prettyFormat($difference / self::MONTH, 'month');
        }
            // Over a year ago
        return self::prettyFormat($difference / self::YEAR, 'year');
    }
    private static function prettyFormat($difference, $unit)
    {
        $prepend = ($difference < 0) ? 'In ' : '';
        $append = ($difference > 0) ? ' ago' : '';
        $difference = floor(abs($difference));
        // If difference is plural, add an 's' to $unit
        if ($difference > 1) {
            $unit = $unit . 's';
        }
        return sprintf('%s%d %s%s', $prepend, $difference, $unit, $append);
    }



}