<?php
declare(strict_types = 1);

namespace Assertis\Util;

use InvalidArgumentException;

/**
 * @author Michał Tatarynowicz <michal.tatarynowicz@assertis.co.uk>
 */
class Weekday
{
    private static $long = [
        0 => 'Monday',
        1 => 'Tuesday',
        2 => 'Wednesday',
        3 => 'Thursday',
        4 => 'Friday',
        5 => 'Saturday',
        6 => 'Sunday',
    ];

    private static $short = [
        0 => 'Mon',
        1 => 'Tue',
        2 => 'Wed',
        3 => 'Thu',
        4 => 'Fri',
        5 => 'Sat',
        6 => 'Sun',
    ];

    /**
     * @var int
     */
    private $dayId;

    /**
     * @param int $dayId
     */
    private function __construct(int $dayId)
    {
        $this->dayId = $dayId;
    }

    /**
     * @param string $string
     * @param array|null $map
     * @return Weekday
     */
    public static function fromString(string $string, array $map = null): Weekday
    {
        if ($map !== null) {
            $key = array_search($string, $map);
        } else {
            $key = array_search($string, self::$long);
            
            if (false === $key) {
                $key = array_search($string, self::$short);
            }
        }

        if (false === $key) {
            throw new InvalidArgumentException(sprintf('Could not parse "%s" as valid weekday name', $string));
        }

        return new Weekday((int)$key);
    }

    /**
     * @return int
     */
    public function getDayId(): int
    {
        return $this->dayId;
    }

    /**
     * @return string
     */
    public function getLongName(): string
    {
        return self::$long[$this->dayId];
    }

    /**
     * @return string
     */
    public function getShortName(): string
    {
        return self::$short[$this->dayId];
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getLongName();
    }
}
