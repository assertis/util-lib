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
        if (!preg_match('/^(\d{4})\-(\d{2})\-(\d{2})$/', $string, $match)) {
            throw new InvalidArgumentException("String \"{$string}\" could not be parsed as date.");
        }

        if ($match[2] < 1 || $match[2] > 12 || $match[3] < 1 || $match[3] > 31) {
            throw new InvalidArgumentException("String \"{$string}\" could not be parsed as date.");
        }

        $date = new static;
        $date->setDate($match[1], $match[2], $match[3]);
        $date->setTime(0, 0, 0);
        return $date;
    }

    /**
     * @param string $format
     * @param string $string
     *
     * @return self
     */
    public static function fromFormat($format, $string)
    {
        $date = parent::createFromFormat($format, $string);
        if (false === $date) {
            throw new InvalidArgumentException("String \"{$string}\" could not be parsed as date.");
        }
        $instance = new static;
        $instance->setTimestamp($date->getTimestamp());

        return $instance;
    }

    /**
     * @param DateTime $dateTime
     * @return self
     */
    public static function fromDateTime(DateTime $dateTime)
    {
        $date = new Date();
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
        return ($this->format('Y-m-d') == $otherDate->format('Y-m-d'));
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
}
