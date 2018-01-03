<?php
declare(strict_types=1);

namespace Assertis\Util;

use PHPUnit_Framework_TestCase;

/**
 * @author Rafał Orłowski <rafal.orlowski@assertis.co.uk>
 */
class AmountPenceListTest extends PHPUnit_Framework_TestCase
{
    public function testAccept()
    {
        $list = new AmountPenceList();
        $list->append(new AmountPence(99));

        $this->assertCount(1, $list);
    }
}
