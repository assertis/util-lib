<?php

namespace Assertis\Util;

use Assertis\Util\DateList;
use PHPUnit_Framework_TestCase;

/**
 * @author Rafał Orłowski <rafal.orlowski@assertis.co.uk>
 */
class DateListTest extends PHPUnit_Framework_TestCase
{

    public function testAccept()
    {
        $dateList = new DateList();
        $this->assertTrue($dateList->accepts(new Date("2016-10-10")));
        $this->assertFalse($dateList->accepts("2016-10-10"));
    }

}