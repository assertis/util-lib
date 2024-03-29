<?php
declare(strict_types=1);

namespace Assertis\Util\Jsend;

use Assertis\Util\Weekdays;
use DateTime;
use JsonSerializable;
use PHPUnit\Framework\TestCase;

/**
 * @author Rafał Orłowski <rafal.orlowski@assertis.co.uk>
 */
class ResponseTest extends TestCase
{
    public function testAcceptsJsonSerializableAsData()
    {
        $data = new Weekdays(true, false, true, false, true, false, true);
        $date = new DateTime('Tue, 19 Dec 2018 12:34:56');

        $response = Response::success($data);
        $response->setDate($date);

        $expected = <<<REQ
HTTP/1.0 200 OK
Cache-Control: no-cache, private
Content-Type:  application/json
Date:          {$date->format('D, d M Y H:i:s')} GMT

{"status":"success","data":{"Mon":true,"Tue":false,"Wed":true,"Thu":false,"Fri":true,"Sat":false,"Sun":true},"links":[]}
REQ;

        static::assertSame(str_replace("\n", "\r\n", $expected), $response->__toString());
    }

    public function testAddsCachingHeaders()
    {
        $data = ['foo' => 'bar'];
        $date = new DateTime('2017-12-19 12:34:56');
        $seconds = 123;

        $response = Response::success($data);
        $response->setDate($date);
        $response->cacheFor($seconds);


        $expected = <<<REQ
HTTP/1.0 200 OK
Cache-Control: max-age={$seconds}, public, s-maxage={$seconds}
Content-Type:  application/json
Date:          {$date->format('D, d M Y H:i:s')} GMT
Expires:       Tue, 19 Dec 2017 12:36:59 GMT

{"status":"success","data":{"foo":"bar"},"links":[]}
REQ;

        static::assertSame(str_replace("\n", "\r\n", $expected), $response->__toString());
    }

    public function testSuccess()
    {
        $data = ['foo' => 'bar'];
        $date = new DateTime('Tue, 19 Dec 2018 12:34:56');

        $response = Response::success($data, Response::HTTP_ACCEPTED, ['name' => 'value'], ['/link' => ['data']]);
        $response->setDate($date);

        $expected = <<<REQ
HTTP/1.0 202 Accepted
Cache-Control: no-cache, private
Content-Type:  application/json
Date:          {$date->format('D, d M Y H:i:s')} GMT
Name:          value

{"status":"success","data":{"foo":"bar"},"links":{"\/link":["data"]}}
REQ;

        static::assertSame(str_replace("\n", "\r\n", $expected), $response->__toString());

        $response = Response::success(['foo' => 'bar']);
        $response->setDate($date);

        $expected = <<<REQ
HTTP/1.0 200 OK
Cache-Control: no-cache, private
Content-Type:  application/json
Date:          {$date->format('D, d M Y H:i:s')} GMT

{"status":"success","data":{"foo":"bar"},"links":[]}
REQ;

        static::assertSame(str_replace("\n", "\r\n", $expected), $response->__toString());
    }

    public function testCreated()
    {
        $data = ['foo' => 'bar'];
        $date = new DateTime('Tue, 19 Dec 2018 12:34:56');

        $response = Response::created('/link', $data, ['name' => 'value']);
        $response->setDate($date);

        $expected = <<<REQ
HTTP/1.0 201 Created
Cache-Control: no-cache, private
Content-Type:  application/json
Date:          {$date->format('D, d M Y H:i:s')} GMT
Location:      /link
Name:          value

{"status":"success","data":{"uri":"\/link","foo":"bar"},"links":[]}
REQ;

        static::assertSame(str_replace("\n", "\r\n", $expected), $response->__toString());

        $response = Response::created('/link');
        $response->setDate($date);

        $expected = <<<REQ
HTTP/1.0 201 Created
Cache-Control: no-cache, private
Content-Type:  application/json
Date:          {$date->format('D, d M Y H:i:s')} GMT
Location:      /link

{"status":"success","data":{"uri":"\/link"},"links":[]}
REQ;

        static::assertSame(str_replace("\n", "\r\n", $expected), $response->__toString());

        $data = new class implements JsonSerializable {
            public function jsonSerialize()
            {
                return ['foo' => 'bar'];
            }
        };

        $response = Response::created('/link', $data);
        $response->setDate($date);

        $expected = <<<REQ
HTTP/1.0 201 Created
Cache-Control: no-cache, private
Content-Type:  application/json
Date:          {$date->format('D, d M Y H:i:s')} GMT
Location:      /link

{"status":"success","data":{"uri":"\/link","foo":"bar"},"links":[]}
REQ;

        static::assertSame(str_replace("\n", "\r\n", $expected), $response->__toString());
    }

    public function testNotFound()
    {
        $date = new DateTime('Tue, 19 Dec 2018 12:34:56');

        $response = Response::notFound('Message', ['name' => 'value']);
        $response->setDate($date);

        $expected = <<<REQ
HTTP/1.0 404 Not Found
Cache-Control: no-cache, private
Content-Type:  application/json
Date:          {$date->format('D, d M Y H:i:s')} GMT
Name:          value

{"status":"fail","message":"Message"}
REQ;

        static::assertSame(str_replace("\n", "\r\n", $expected), $response->__toString());

        $response = Response::notFound();
        $response->setDate($date);

        $expected = <<<REQ
HTTP/1.0 404 Not Found
Cache-Control: no-cache, private
Content-Type:  application/json
Date:          {$date->format('D, d M Y H:i:s')} GMT

{"status":"fail"}
REQ;

        static::assertSame(str_replace("\n", "\r\n", $expected), $response->__toString());
    }

    public function testFail()
    {
        $data = ['foo' => 'bar'];
        $date = new DateTime('Tue, 19 Dec 2018 12:34:56');

        $response = Response::fail('Message', 'FAIL-CODE', $data, Response::HTTP_FORBIDDEN, ['name' => 'value']);
        $response->setDate($date);

        $expected = <<<REQ
HTTP/1.0 403 Forbidden
Cache-Control: no-cache, private
Content-Type:  application/json
Date:          {$date->format('D, d M Y H:i:s')} GMT
Name:          value

{"status":"fail","message":"Message","code":"FAIL-CODE","data":{"foo":"bar"}}
REQ;

        static::assertSame(str_replace("\n", "\r\n", $expected), $response->__toString());

        $response = Response::fail();
        $response->setDate($date);

        $expected = <<<REQ
HTTP/1.0 400 Bad Request
Cache-Control: no-cache, private
Content-Type:  application/json
Date:          {$date->format('D, d M Y H:i:s')} GMT

{"status":"fail"}
REQ;

        static::assertSame(str_replace("\n", "\r\n", $expected), $response->__toString());
    }

    public function testError()
    {
        $data = ['foo' => 'bar'];
        $date = new DateTime('Tue, 19 Dec 2018 12:34:56');

        $response = Response::error('Message', 'FAIL-CODE', $data, Response::HTTP_FORBIDDEN, ['name' => 'value']);
        $response->setDate($date);

        $expected = <<<REQ
HTTP/1.0 403 Forbidden
Cache-Control: no-cache, private
Content-Type:  application/json
Date:          {$date->format('D, d M Y H:i:s')} GMT
Name:          value

{"status":"error","message":"Message","code":"FAIL-CODE","data":{"foo":"bar"}}
REQ;

        static::assertSame(str_replace("\n", "\r\n", $expected), $response->__toString());

        $response = Response::error('Message');
        $response->setDate($date);

        $expected = <<<REQ
HTTP/1.0 500 Internal Server Error
Cache-Control: no-cache, private
Content-Type:  application/json
Date:          {$date->format('D, d M Y H:i:s')} GMT

{"status":"error","message":"Message"}
REQ;

        static::assertSame(str_replace("\n", "\r\n", $expected), $response->__toString());
    }
}
