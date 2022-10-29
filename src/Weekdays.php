<?php

/**
 * @author Michał Tatarynowicz <michal@assertis.co.uk>
 */

namespace Assertis\Util;

use InvalidArgumentException;
use JsonSerializable;

/**
 * Class Weekdays
 * @package Assertis\Util
 */
class Weekdays implements JsonSerializable
{

    /**
     * @var bool
     */
    private $monday;

    /**
     * @var bool
     */
    private $tuesday;

    /**
     * @var bool
     */
    private $wednesday;

    /**
     * @var bool
     */
    private $thursday;

    /**
     * @var bool
     */
    private $friday;

    /**
     * @var bool
     */
    private $saturday;

    /**
     * @var bool
     */
    private $sunday;

    /**
     * @param bool $monday
     * @param bool $tuesday
     * @param bool $wednesday
     * @param bool $thursday
     * @param bool $friday
     * @param bool $saturday
     * @param bool $sunday
     */
    public function __construct($monday, $tuesday, $wednesday, $thursday, $friday, $saturday, $sunday)
    {
        $this->monday = (bool)$monday;
        $this->tuesday = (bool)$tuesday;
        $this->wednesday = (bool)$wednesday;
        $this->thursday = (bool)$thursday;
        $this->friday = (bool)$friday;
        $this->saturday = (bool)$saturday;
        $this->sunday = (bool)$sunday;
    }

    /**
     * @param string $data
     * @return Weekdays
     */
    public static function fromString($data): Weekdays
    {
        if (!preg_match('/^([A-Z][a-z]{2},)*[A-Z][a-z]{2}$/', $data)) {
            throw new InvalidArgumentException("Input {$data} is not a valid Weekdays information string.");
        }
        $days = explode(',', $data);
        return new self(
            in_array('Mon', $days),
            in_array('Tue', $days),
            in_array('Wed', $days),
            in_array('Thu', $days),
            in_array('Fri', $days),
            in_array('Sat', $days),
            in_array('Sun', $days)
        );
    }

    /**
     * @return boolean
     */
    public function monday(): bool
    {
        return $this->monday;
    }

    /**
     * @return boolean
     */
    public function tuesday(): bool
    {
        return $this->tuesday;
    }

    /**
     * @return boolean
     */
    public function wednesday(): bool
    {
        return $this->wednesday;
    }

    /**
     * @return boolean
     */
    public function thursday(): bool
    {
        return $this->thursday;
    }

    /**
     * @return boolean
     */
    public function friday(): bool
    {
        return $this->friday;
    }

    /**
     * @return boolean
     */
    public function saturday(): bool
    {
        return $this->saturday;
    }

    /**
     * @return boolean
     */
    public function sunday(): bool
    {
        return $this->sunday;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'Mon' => $this->monday(),
            'Tue' => $this->tuesday(),
            'Wed' => $this->wednesday(),
            'Thu' => $this->thursday(),
            'Fri' => $this->friday(),
            'Sat' => $this->saturday(),
            'Sun' => $this->sunday(),
        ];
    }

    /**
     * @param string $empty
     * @return string
     */
    public function short($empty = '-'): string
    {
        $out = '';
        foreach ($this->toArray() as $key => $val) {
            $out .= $val ? $key[0] : $empty;
        }
        return $out;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return implode(', ', array_keys(array_filter($this->jsonSerialize())));
    }

    /**
     * @param Date $date
     * @return bool
     */
    public function matches(Date $date): bool
    {
        return (bool)$this->toArray()[$date->format('D')];
    }
}
