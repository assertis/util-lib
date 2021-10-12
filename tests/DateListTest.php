<?php

namespace Assertis\Util;

use PHPUnit\Framework\TestCase;

/**
 * @author Rafał Orłowski <rafal.orlowski@assertis.co.uk>
 */
class DateListTest extends TestCase
{

    public function testAccept()
    {
        $dateList = new DateList();
        $this->assertTrue($dateList->accepts(new Date("2016-10-10")));
        $this->assertFalse($dateList->accepts("2016-10-10"));
    }

}
