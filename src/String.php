<?php

namespace Assertis\Util;

/**
 * Class String
 *
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
     *
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
     *
     * @return string
     */
    public static function ccwords($word)
    {
        $spaced = str_replace('-', ' ', $word);
        $ucworded = String::ucwords($spaced);

        return str_replace(' ', '', $ucworded);
    }

    /**
     * @param string $text
     * @param int $start
     * @param int|null $length
     *
     * @return string
     */
    public static function substr($text, $start = 0, $length = null)
    {
        $lastTextIndex = strlen($text) - 1;
        if (strlen($text) === 0) {
            return $text;
        } else if ($lastTextIndex < $start) {
            return '';
        }

        return substr($text, $start, $length);
    }
}
