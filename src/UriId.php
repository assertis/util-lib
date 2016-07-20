<?php

namespace Assertis\Util;

use InvalidArgumentException;

/**
 * @author Michał Tatarynowicz <michal.tatarynowicz@assertis.co.uk>
 */
abstract class UriId extends Id
{
    private static $VALUE_REGEX = '|^(/[a-z0-9\-_\.\?\=]+)+$|';

    /**
     * @return string
     */
    protected function getValueRegex()
    {
        return self::$VALUE_REGEX;
    }

    /**
     * @param mixed $value
     * @return string
     */
    protected function assertIsValidUri($value)
    {
        if (preg_match($this->getValueRegex(), $value)) {
            return;
        }

        throw new InvalidArgumentException(sprintf(
            '%s is not a valid value for %s',
            $value,
            get_class($this)
        ));
    }

    /**
     * @inheritdoc
     */
    public function __construct($value)
    {
        $this->assertIsValidUri($value);

        parent::__construct($value);
    }
}
