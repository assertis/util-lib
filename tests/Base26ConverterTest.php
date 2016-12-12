<?php

namespace Assertis\Util;

use PHPUnit_Framework_TestCase;

/**
 * Unit test for the {@link Base26Converter} utility class.
 * @author Daniel Dyer
 */
class Base26ConverterTest extends PHPUnit_Framework_TestCase
{

    /**
     * Make sure that a given test string encodes to the correct string
     * (verified against the sample Java code from the RSP barcode spec)
     * and that that encoded string decodes to the original input.
     */
    public function testRoundTrip()
    {
        $original = 'MyTrainTicket.co.uk';
        $bytes = [];

        foreach (str_split($original) as $char) {
            $bytes[] = ord($char);
        }

        $encoded = Base26Converter::encode($bytes);
        $this->assertEquals('RYLAWHIINRJUVEKHCBHAAZGCDTWZTVTGB', $encoded);
        $decoded = Base26Converter::decode($encoded);
        $decodedString = '';

        foreach ($decoded as $byte) {
            // Crappy Base26 code means sometimes there is an extra null byte at the end.
            if (strlen($decodedString) < strlen($original)) {
                $decodedString .= chr($byte);
            }
        }

        $this->assertEquals($original, $decodedString);
    }
}

