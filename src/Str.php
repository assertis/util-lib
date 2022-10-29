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
final class Str
{

    /**
     * First change all words to lower
     *
     * @param $string
     *
     * @return string
     */
    public static function ucwords($string): string
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
    public static function ccwords($word): string
    {
        $spaced = str_replace('-', ' ', $word);
        $ucworded = self::ucwords($spaced);

        return str_replace(' ', '', $ucworded);
    }

    /**
     * @param string $text
     * @param int $start
     * @param int|null $length
     *
     * @return string
     */
    public static function substr($text, $start = 0, $length = null): string
    {
        $lastTextIndex = strlen($text) - 1;
        if (strlen($text) === 0) {
            return $text;
        }
        if ($lastTextIndex < $start) {
            return '';
        }

        if (null === $length) {
            return substr($text, $start);
        }

        return substr($text, $start, $length);
    }

    /**
     * @param string $text
     * @param int $charsPerLine
     * @return string
     */
    public static function wrap($text, $charsPerLine): string
    {
        $inputLines = explode("\n", $text);
        $outputLines = [];

        foreach ($inputLines as $inputLine) {
            $spaceLeft = $charsPerLine;
            $inputWords = explode(' ', $inputLine);

            $outputLine = [];

            foreach ($inputWords as $inputWord) {
                if ($spaceLeft < strlen($inputWord)) {
                    $outputLines[] = implode(' ', $outputLine);
                    $outputLine = [];
                    $spaceLeft = $charsPerLine;
                }

                $spaceLeft -= strlen($inputWord) + (count($outputLine) > 0 ? 1 : 0);
                $outputLine[] = $inputWord;
            }

            if (count($outputLine) > 0) {
                $outputLines[] = implode(' ', $outputLine);
            }
        }

        return implode("\n", $outputLines);
    }
}
