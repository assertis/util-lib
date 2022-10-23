<?php
declare(strict_types=1);

namespace Assertis\Util;

use PHPUnit\Framework\TestCase;

/**
 * @author Rafał Orłowski <rafal.orlowski@assertis.co.uk>
 */
class AmountPenceListTest extends TestCase
{
    public function testAccept()
    {
        $list = new AmountPenceList();
        $list->append(new AmountPence(99));

        $this->assertCount(1, $list);
    }
}
