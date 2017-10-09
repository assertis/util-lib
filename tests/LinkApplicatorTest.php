<?php
declare(strict_types=1);

namespace Assertis\Link\Data;

use Assertis\Util\LinkApplicator;
use PHPUnit\Framework\TestCase;

/**
 * @author Michał Tatarynowicz <michal.tatarynowicz@assertis.co.uk>
 */
class LinkApplicatorTest extends TestCase
{
    public function testItResolvesLinks()
    {
        $data = ['uri' => '/one', 'rel' => '/two'];

        $links = [
            '/two'   => ['uri' => '/two', 'rel' => '/three'],
            '/three' => ['uri' => '/three'],
        ];

        $expected = [
            'uri' => '/one',
            'rel' => [
                'uri' => '/two',
                'rel' => [
                    'uri' => '/three',
                ],
            ],
        ];

        $service = new LinkApplicator();
        $actual = $service->apply($data, $links);

        self::assertSame($expected, $actual);
    }

    public function testItIgnoresCircularReferences()
    {
        $data = ['uri' => '/one', 'rel' => '/two'];

        $links = [
            '/two'   => ['uri' => '/two', 'rel' => '/three'],
            '/three' => ['uri' => '/three', 'rel' => '/four'],
            '/four'  => ['uri' => '/four', 'rel' => '/one'],
        ];

        $expected = [
            'uri' => '/one',
            'rel' => [
                'uri' => '/two',
                'rel' => [
                    'uri' => '/three',
                    'rel' => [
                        'uri' => '/four',
                        'rel' => '/one',
                    ],
                ],
            ],
        ];

        $service = new LinkApplicator();
        $actual = $service->apply($data, $links);

        self::assertSame($expected, $actual);
    }
}
