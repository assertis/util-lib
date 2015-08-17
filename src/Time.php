<?php

/**
 * @author Michał Tatarynowicz <michal@assertis.co.uk>
 */

namespace Assertis\Util;

use JsonSerializable;
use UnexpectedValueException;

/**
 * Class Time
 * @package Assertis\Util
 */
class Time implements JsonSerializable
{

    const NRS_FORMAT = '/^([0-2]\d)([0-5]\d)$/';
    const DEFAULT_FORMAT = '/^([0-2]?\d)\:([0-5]\d)$/';

    /**
     * @var int
     */
    private $hours;

    /**
     * @var int
     */
    private $minutes;

    /**
     * @param $string
     * @return Time|null
     * @throws UnexpectedValueException
     */
    public static function fromString($string)
    {
        if ('' === trim($string)) {
            return null;
        }

        if (preg_match(self::DEFAULT_FORMAT, $string, $match)) {
            return new self((int)$match[1], (int)$match[2]);
        } elseif (preg_match(self::NRS_FORMAT, $string, $match)) {
            return new self((int)$match[1], (int)$match[2]);
        } else {
            throw new UnexpectedValueException("Could not parse \"{$string}\" as time.");
        }
    }

    /**
     * @param int $hours
     * @param int $minutes
     */
    public function __construct($hours, $minutes)
    {
        $this->hours = $hours;
        $this->minutes = $minutes;
    }

    /**
     * @return int
     */
    public function getHours()
    {
        return $this->hours;
    }

    /**
     * @return int
     */
    public function getMinutes()
    {
        return $this->minutes;
    }

    /**
     * @return string
     */
    public function getTime()
    {
        return $this->hours . ':' . sprintf('%02d', $this->minutes);
    }

    /**
     * @return int
     */
    public function toInt()
    {
        return (int)($this->hours . sprintf('%02d', $this->minutes));
    }

    /**
     * @param Time $other
     * @return bool
     */
    public function isAfter(Time $other)
    {
        return $this->toInt() > $other->toInt();
    }

    /**
     * @param Time $other
     * @return bool
     */
    public function isBefore(Time $other)
    {
        return $this->toInt() < $other->toInt();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getTime();
    }

    /**
     * @return string
     */
    public function jsonSerialize()
    {
        return $this->getTime();
    }
}
