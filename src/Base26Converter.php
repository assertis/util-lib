<?php

namespace Assertis\Util;

/**
 * Utility methods for encoding/decoding bytes as base 26 strings.
 * Used for barcodes on self-print and mobile tickets.
 *
 * This code is a direct PHP translation of the sample Java code in
 * the RSP barcode spec.  So if you think it's ugly, don't blame me.
 *
 * @author Daniel Dyer
 */
class Base26Converter
{

    private static $base26Digits = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    private static $base26Nom = 851;
    private static $base26Denom = 500;

    /**
     * @param array $bytes An array of bytes (0-255) to be encoded.
     * @return string A string containing only upper case letters.
     */
    public static function encode(array $bytes): string
    {
        $length = count($bytes);
        // this size is going to be larger than required
        // This is fairly close to the 1.17519... ratio (actual value below is 1.1752)
        $overEstSize = (int)((($length * self::$base26Nom) + self::$base26Denom - 1) / self::$base26Denom);
        //create a new temporary array to hold the base26 values (from 0 to 25) that
        //are calculated for the output message
        $base26byteArray = [];
        //the actual base conversion
        for ($x = 0; $x < $overEstSize; $x++) {
            $accumulator = 0;
            for ($i = $length - 1; $i >= 0; $i--) {
                $v = ($accumulator * 256) + ($bytes[$i] & 0xFF);
                $bytes[$i] = (int)($v / 26);
                $accumulator = $v - (($bytes[$i] & 0xFF) * 26);
            }
            $base26byteArray[$x] = $accumulator;
        }
        // convert from a base26 array into a base26 character String
        $encChars = [];
        for ($x = 0; $x < $overEstSize; $x++) {
            //simply pick out the symbol that represents this value from 0 to 25
            //from the source characters and put them into the output char array
            $encChars[$x] = self::$base26Digits[$base26byteArray[$x]];
        }

        //wrap the char array in a string for easy handling
        return implode($encChars);
    }


    /**
     * @param string $string A string containing only upper case letters to be decoded.
     * @return array An array of bytes (0-255).
     */
    public static function decode($string): array
    {
        $length = strlen($string);
        //temporary array to hold the input values
        $inputCharValues = [];
        //fill the temporary array with the input char values
        for ($i = 0; $i < $length; $i++) {
            $inputCharValues[$i] = ord($string[$i]) - ord('A');
        }
        // estimate the result array (might be oversized by 1 byte)
        $outSize = (int)(($length * self::$base26Denom + self::$base26Nom - 1) / self::$base26Nom);
        $outputByteArray = [];
        // out might have an extra character '\u0000' at the end !
        for ($p = 0; $p < $outSize; $p++) {
            //reset accumulator to zero
            $accumulator = 0;
            for ($i = $length - 1; $i >= 0; $i--) {
                $v = $accumulator * 26 + ($inputCharValues[$i] & 0xFF);
                $inputCharValues[$i] = (int)($v / 256);
                $accumulator = $v & 0xFF;
            }
            $outputByteArray[$p] = $accumulator;
        }

        return $outputByteArray;
    }
}
