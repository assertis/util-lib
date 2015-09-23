<?php

/**
 * @author Michał Tatarynowicz <michal@assertis.co.uk>
 */

namespace Assertis\Util;

use DateInterval;
use DateTime;
use InvalidArgumentException;
use JsonSerializable;

/**
 * Class Date
 * @package Assertis\Util
 */
class Date extends DateTime implements JsonSerializable
{
    const LONG_INPUT_FORMAT = '/^(\d{4})\-(\d{2})\-(\d{2}) (\d{2})\:(\d{2})\:(\d{2})$/';
    const SHORT_INPUT_FORMAT = '/^(\d{4})\-(\d{2})\-(\d{2})$/';

    const SHORT_FORMAT = 'Y-m-d';
    const LONG_FORMAT = 'Y-m-d H:i:s';
    const ENGLISH_FORMAT = 'd-m-Y';

    /**
     * @param string $string
     * @return self
     * @throws InvalidArgumentException
     */
    public static function fromString($string)
    {
        if (preg_match(self::LONG_INPUT_FORMAT, $string, $match)) {
            $date = [ $match[1], $match[2], $match[3] ];
            $time = [ $match[4], $match[5], $match[6] ];
        } elseif (preg_match(self::SHORT_INPUT_FORMAT, $string, $match)) {
            $date = [ $match[1], $match[2], $match[3] ];
            $time = [ 0, 0, 0 ];
        } else {
            throw new InvalidArgumentException("String \"{$string}\" could not be parsed as date.");
        }

        $out = new static;
        
        if (false === $out->setDate($date[0], $date[1], $date[2])) {
            throw new InvalidArgumentException("String \"{$string}\" could not be parsed as date.");
        };
        
        if (false === $out->setTime($time[0], $time[1], $time[2])) {
            throw new InvalidArgumentException("String \"{$string}\" could not be parsed as date.");
        };
        
        return $out;
    }

    /**
     * @param string $format
     * @param string $string
     *
     * @return self
     */
    public static function fromFormat($format, $string)
    {
        $dateTime = parent::createFromFormat($format, $string);
        if (false === $dateTime) {
            throw new InvalidArgumentException("String \"{$string}\" could not be parsed as date.");
        }
        
        return static::fromDateTime($dateTime);
    }

    /**
     * @param DateTime $dateTime
     * @return self
     */
    public static function fromDateTime(DateTime $dateTime)
    {
        $date = new static;
        $date->setTimestamp($dateTime->getTimestamp());
        
        return $date;
    }

    /**
     * @return boolean
     */
    public function isSaturday()
    {
        return (int)$this->format('w') === 6;
    }

    /**
     * @return bool
     */
    public function isSunday()
    {
        return (int)$this->format('w') === 0;
    }

    /**
     * @return bool
     */
    public function isWorkingDay()
    {
        $day = (int)$this->format('w');
        
        return !in_array($day, [6, 0]);
    }

    /**
     * @return Date
     */
    public function getDayEarlier()
    {
        return $this->getDaysEarlier(1);
    }

    /**
     * @param int $days
     * @return Date
     */
    public function getDaysEarlier($days)
    {
        $date = clone $this;

        return $date->sub(new DateInterval("P{$days}D"));
    }

    /**
     * @param int $days
     * @return Date
     */
    public function getWorkingDaysEarlier($days)
    {
        if ($days < 0) {
            return $this->getWorkingDaysLater(abs($days));
        }

        $date = clone $this;

        while ($days > 0) {
            if ($date->isWorkingDay()) {
                $days--;
            }

            $date = $date->getDayEarlier();
        }

        return $date;
    }

    /**
     * @param int $days
     * @return Date
     */
    public function getWorkingDaysLater($days)
    {
        if ($days < 0) {
            return $this->getWorkingDaysEarlier(abs($days));
        }

        $date = clone $this;

        while ($days > 0) {
            $date = $date->getDayLater();

            if ($date->isWorkingDay()) {
                $days--;
            }
        }

        return $date;
    }

    /**
     * @return Date
     */
    public function getDayLater()
    {
        return $this->getDaysLater(1);
    }

    /**
     * @param int $days
     * @return Date
     */
    public function getDaysLater($days)
    {
        $date = clone $this;

        return $date->add(new DateInterval("P{$days}D"));
    }

    /**
     * @param int $months
     * @return Date
     */
    public function getMonthsLater($months)
    {
        $date = clone $this;

        return $date->add(new DateInterval("P{$months}M"));
    }

    /**
     * @return string
     */
    public function formatShort()
    {
        return $this->format(self::SHORT_FORMAT);
    }

    /**
     * @return string
     */
    public function formatLong()
    {
        return $this->format(self::LONG_FORMAT);
    }

    /**
     * @return string
     */
    public function formatEnglish()
    {
        return $this->format(self::ENGLISH_FORMAT);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->formatShort();
    }

    /**
     * @return mixed|string
     */
    public function jsonSerialize()
    {
        return $this->formatShort();
    }

    /**
     * @param Date $otherDate
     * @return bool
     */
    public function isBefore(Date $otherDate)
    {
        return $otherDate > $this;
    }

    /**
     * @param Date $otherDate
     * @return bool
     */
    public function isAfter(Date $otherDate)
    {
        return $otherDate < $this;
    }

    /**
     * @param Date $otherDate
     * @return bool
     */
    public function isSameDay(Date $otherDate)
    {
        return ($this->format('Y-m-d') === $otherDate->format('Y-m-d'));
    }

    /**
     * @param Date $otherDate
     * @return bool
     */
    public function isDifferentDay(Date $otherDate)
    {
        return !$this->isSameDay($otherDate);
    }

    /**
     * @return Date
     */
    public static function getCurrent()
    {
        return new self();
    }

    /**
     * @return bool
     */
    public function isInThePast()
    {
        return $this->isBefore(new self);
    }

    /**
     * @return bool
     */
    public function isInTheFuture()
    {
        return $this->isAfter(new self);
    }

    /**
     * @return Date
     */
    public function getDaysEnd()
    {
        return new self($this->formatShort() . " 23:59:59");
    }

    /**
     * Get the 2 bit year for CCST mag stripe
     *
     * Binary value of year, in the range 0 to 3, i.e. a single digit in
     * a repeating 4 year cycle commencing in 1981 with the year
     * 0, 2002 would therefore be 1.
     *
     * @return string
     */
    public function getTwoBitYear()
    {
        $year = (int)$this->format('Y');
        $year -= 1981;

        return $year % 4;
    }

    /**
     * @return integer The day of the month (1-31).
     */
    public function getDayOfMonth()
    {
        return (int)$this->format('j');
    }

    /**
     * @return integer The month of the year (1-12).
     */
    public function getMonthOfYear()
    {
        return (int)$this->format('n');
    }

    /**
     * Get the 9 bit date for CCST mag stripe
     *
     * Encode the binary value of the date shown below, where the range is 000 to
     * 511 where, 14th Jan 2008 = 000, 1st Jan 2009 =353, 1st Jan 2010 = 206.
     * Another way of looking at this is to number days consecutively starting from
     * a base date of 0=1 Jan 1980. This gives 10593=1 Jan 2009. The 9 least
     * significant bits of the binary equivalent of a particular date’s number then
     * gives the required encoding.
     *
     * @return string
     */
    public function getNineBitDate()
    {
        $nineteenEighty = strtotime('1980-01-01 00:00:00');
        $diff = ceil(($this->getTimestamp() - $nineteenEighty) / 86400);

        return $diff % 512;
    }

    /**
     * Get the 10 bit date for CCST mag stripe (used for season tickets, normal tickets use 9-bit date).
     *
     * Encode the binary value of the date shown below, where the range is 000 to
     * 1023 where, 14th Jan 2008 = 000, 1st Jan 2009 =353, 1st Jan 2010 = 718.
     * Another way of looking at this is to number days consecutively starting from
     * a base date of 0=1 Jan 1980. This gives 10593=1 Jan 2009. The 10 least
     * significant bits of the binary equivalent of a particular date’s number then
     * gives the required encoding.
     *
     * @return int
     */
    public function getTenBitDate()
    {
        $nineteenEighty = strtotime('1980-01-01 00:00:00');
        $diff = ceil(($this->getTimestamp() - $nineteenEighty) / 86400);

        return $diff % 1024;
    }


    /**
     * Get the 9 bit minute of the day for CCST mag stripe
     *
     * The times are processed and encoded in 5-minute blocks, in
     * the range 100 to 387 with 0000 - 0004hrs = 100, 0005 - 0009hrs =
     * 101, etc.
     *
     * @return int
     */
    public function getNineBitMinuteOfDay()
    {
        $min = ceil(date("i", $this->getTimestamp()) / 5);
        $hour = floor(date("H", $this->getTimestamp()) * 12);
        
        return (int)(100 + $min + $hour - 1);
    }

}
