<?php

namespace Assertis\Util;

use PHPUnit\Framework\TestCase;

/**
 * @author Michał Tatarynowicz <michal@assertis.co.uk>
 */
class XmlTest extends TestCase
{
    public function testFind()
    {
        $xml = new XML('<foo><bar id="1"/></foo>');

        $bar = $xml->find('/foo/bar');

        $this->assertInstanceOf(XML::class, $bar);
        $this->assertSame('1', $bar->attr('id'));
    }

    public function testFindNs()
    {
        $xml = new XML('<foo xmlns="http://example.com"><bar id="1"/></foo>');

        $bar = $xml->findNs('ns', '/ns:foo/ns:bar');

        $this->assertInstanceOf(XML::class, $bar);
        $this->assertSame('1', $bar->attr('id'));
    }
}
