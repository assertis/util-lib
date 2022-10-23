<?php

namespace Assertis\Util;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @author Michał Tatarynowicz <michal@assertis.co.uk>
 */
class UriIdTest extends TestCase
{
    /**
     * @return array
     */
    public function provideValidation()
    {
        return [
            ['/foo', true],
            ['/foo/1', true],
            ['/foo/bar', true],
            ['/foo/bar/baz/boo/woot/foot/doot/12/3/4/5678', true],
            ['/f-o_o/bar.baz?loo=foo', true],
            ['/', false],
            ['//foo//bar', false],
            ['/æąąśðæę©', false],
            ['', false],
        ];
    }

    /**
     * @dataProvider provideValidation
     * @param string $uri
     * @param bool $isValid
     */
    public function testValidation($uri, $isValid)
    {
        if (!$isValid) {
            $this->expectException(InvalidArgumentException::class);
        }

        $this->getMockForAbstractClass(UriId::class, [$uri]);

        $this->expectNotToPerformAssertions();
    }
}
