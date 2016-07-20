<?php

namespace Assertis\Util;

use InvalidArgumentException;

/**
 * @author Michał Tatarynowicz <michal.tatarynowicz@assertis.co.uk>
 */
abstract class UriId extends Id
{
    const REGEX = '|^(/[a-z0-9\-_\.\?\=]+)+$|';

    /**
     * @return string
     */
    protected function getValueRegex()
    {
        return self::REGEX;
    }

    /**
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
