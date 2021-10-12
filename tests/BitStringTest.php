<?php

namespace Assertis\Util;

use PHPUnit\Framework\TestCase;

class BitStringTest extends TestCase
{

    /**
     * Check that a bit string is constructed correctly, with
     * the correct length and all bits initially set to zero.
     */
    public function testCreateBitString()
    {
        $bitString = new BitString(100);
        $this->assertEquals(100, $bitString->getLength());
        for ($i = 0; $i < $bitString->getLength(); $i++) {
            $this->assertFalse($bitString->getBit($i));
        }
    }

    /**
     * Make sure that bits are set correctly.
     */
    public function testSetBits()
    {
        $bitString = new BitString(5);
        $bitString->setBit(1, true);
        $bitString->setBit(4, true);
        // Testing with non-symmetrical string to ensure that there are no endian
        // problems.
        $this->assertFalse($bitString->getBit(0));
        $this->assertTrue($bitString->getBit(1));
        $this->assertFalse($bitString->getBit(2));
        $this->assertFalse($bitString->getBit(3));
        $this->assertTrue($bitString->getBit(4));
        // Test unsetting a bit.
        $bitString->setBit(4, false);
        $this->assertFalse($bitString->getBit(4));
    }

    public function testSetMultipleBits()
    {
        $bitString = new BitString(5);
        $bitString->setBitsHighToLow(3, '110');
        $this->assertFalse($bitString->getBit(0));
        $this->assertFalse($bitString->getBit(1));
        $this->assertTrue($bitString->getBit(2));
        $this->assertTrue($bitString->getBit(3));
        $this->assertFalse($bitString->getBit(4));
    }

    public function testGetByte()
    {
        $bitString = new BitString(10);
        $bitString->setBitsHighToLow(9, '1010101010');
        $byte = $bitString->getByte(8);
        $this->assertEquals(85, $byte);
    }

    public function testGetBytes()
    {
        $bitString = new BitString(26);
        $bitString->setBitsHighToLow(25, '0111111110000000011110000');
        $bytes = $bitString->getBytes(24, 3);
        // Should be 3 bytes.
        $this->assertEquals(3, sizeof($bytes));
        $this->assertEquals(255, $bytes[0]);
        $this->assertEquals(0, $bytes[1]);
        $this->assertEquals(240, $bytes[2]);
    }

    public function testHexString()
    {
        $bitString = new BitString(16);
        $bitString->setBitsHighToLow(15, '0111001011101000');
        $this->assertEquals('72E8', $bitString->toHexString());
    }

    /**
     * This is a regression test for problems converting strings that spanned multiple words.
     */
    public function testLongHexString()
    {
        $bitString = new BitString(48);
        $bitString->setBitsHighToLow(47, '011100101110100001110010111010000111001011101000');
        $this->assertEquals('72E872E872E8', $bitString->toHexString());
    }

    /**
     * This is a regression test for problems converting strings that map to hex strings with leading zeros.
     */
    public function testHexStringLeadingZeros()
    {
        $bitString = new BitString(16);
        $bitString->setBitsHighToLow(15, '0000000010101000');
        $this->assertEquals('00A8', $bitString->toHexString());
    }

    /**
     * This is a regression test for problems converting strings that spanned multiple words.
     */
    public function testLongHexStringAllZeros()
    {
        $bitString = new BitString(48);
        $bitString->setBitsHighToLow(47, '000000000000000000000000000000000000000000000000');
        $this->assertEquals('000000000000', $bitString->toHexString());
    }

    /**
     * Make sure bit-flipping works as expected.
     */
    public function testFlipBits()
    {
        $bitString = new BitString(5);
        $bitString->flipBit(2);
        $this->assertTrue($bitString->getBit(2));
        $bitString->flipBit(2);
        $this->assertFalse($bitString->getBit(2));
    }

    /**
     * Checks that binary string representations are correctly generated.
     */
    public function testBinaryString()
    {
        $bitString = new BitString(10);
        $bitString->setBit(3, true);
        $bitString->setBit(7, true);
        $bitString->setBit(8, true);
        $string = $bitString->toBinaryString();
        // Testing with leading zero to make sure it isn't omitted.
        $this->assertEquals("0110001000", $string);
    }

    public function testConvertToString()
    {
        $bitString = new BitString(32);
        $bitString->setBitsHighToLow(31, '11111111111111111111111111111111');
        $string = $bitString->__toString();
        // 32-bits should map to 4 8-bit characters.
        $this->assertEquals(4, strlen($string));
        foreach (str_split($string) as $char) {
            $this->assertEquals(chr(255), $char);
        }
    }

    /**
     * String size should correspond to the number of whole bytes (not words).  Therefore
     * If there are 5 bytes, the string should be 5 characters long.  It should not be 8,
     * even though internally the BitString uses 2 4-byte words.
     */
    public function testConvertToStringPartialWord()
    {
        $bitString = new BitString(40);
        $bitString->setBitsHighToLow(39, '1111111111111111111111111111111111111111');
        $string = $bitString->__toString();
        // 32-bits should map to 4 8-bit characters.
        $this->assertEquals(5, strlen($string));
        foreach (str_split($string) as $char) {
            $this->assertEquals(255, ord($char));
        }
    }

    public function testConvertToStringPartialByte()
    {
        $bitString = new BitString(10);
        $bitString->setBitsHighToLow(9, '1111111111');
        $string = $bitString->__toString();
        // 10-bits should map to 2 8-bit characters.
        $this->assertEquals(2, strlen($string));
        $this->assertEquals(3, ord($string[0]));
        $this->assertEquals(255, ord($string[1]));
    }

    public function testConvertToStringMultiWord()
    {
        $bitString = new BitString(64);
        $bitString->setBitsHighToLow(63, '1111111111111111111111111111111100000000000000000000000000000000');
        $string = $bitString->__toString();
        // 64-bits should map to 8 8-bit characters.
        $this->assertEquals(8, strlen($string));
        $chars = str_split($string);
        $this->assertEquals(chr(255), $chars[0]);
        $this->assertEquals(chr(0), $chars[count($chars) - 1]);
        $bitString2 = BitString::fromString($string);
        $this->assertEquals(
            '1111111111111111111111111111111100000000000000000000000000000000',
            $bitString2->toBinaryString()
        );
    }

    public function testConvertFromString()
    {
        $string = chr(255) . chr(254) . chr(253);
        $bitString = BitString::fromString($string);
        $this->assertEquals(24, $bitString->getLength());
        $this->assertEquals('111111111111111011111101', $bitString->toBinaryString());
        // BitString should convert back to the original string.
        $string2 = $bitString->__toString();
        $this->assertEquals($string, $string2);
    }
}
