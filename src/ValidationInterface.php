<?php

namespace Assertis\Util;

use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * Interface ValidationInterface
 * @package Assertis\Util
 * @author Maciej Romanski <maciej.romanski@assertis.co.uk>
 *
 * Interface to provide validation method
 */
interface ValidationInterface {

    /**
     * Method to loadValidatorMetadata field
     * @param ClassMetadata $metadata
     */
    public static function loadValidatorMetadata(ClassMetadata $metadata);

}