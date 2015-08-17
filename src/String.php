<?php

namespace Assertis\Util;

/**
 * Class String
 * @package Assertis\Util
 * @author Maciej Romanski <maciej.romanski@assertis.co.uk>
 *
 * Provide util methods for strings
 */
final class String
{

    /**
     * First change all words to lower
     *
     * @param $string
     * @return string
     */
    public static function ucwords($string)
    {
        return ucwords(strtolower($string));
    }

    /**
     * Change dashed word to camelCase word
     *
     * @param string $word
     * @return string
     */
    public static function ccwords($word)
    {
        $spaced = str_replace('-', ' ', $word);
        $ucworded = String::ucwords($spaced);

        return str_replace(' ', '', $ucworded);
    }
}