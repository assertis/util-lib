<?php
declare(strict_types=1);

namespace Assertis\Util;

use ArrayIterator;
use Assertis\Util\Http;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * @author Michał Tatarynowicz <michal@assertis.co.uk>
 */
class RequestExceptionsTest extends TestCase
{

    public function testGetHttpCode()
    {
        static::assertSame(Response::HTTP_FORBIDDEN, (new Http\AccessDeniedException())->getHttpCode());
        static::assertSame(Response::HTTP_BAD_REQUEST, (new Http\BadRequestException())->getHttpCode());
        static::assertSame(Response::HTTP_NOT_FOUND, (new Http\NotFoundException())->getHttpCode());
        static::assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, (new Http\ServerErrorException())->getHttpCode());
    }

    public function testValidationException()
    {
        $propertyPath = '/foo/bar';
        $message = 'Message.';

        $violationMock = $this->createMock(ConstraintViolation::class);
        $violationMock->expects(static::once())->method('getPropertyPath')->willReturn($propertyPath);
        $violationMock->expects(static::once())->method('getMessage')->willReturn($message);

        $iterator = new ArrayIterator([$violationMock]);

        /** @var ConstraintViolationList|MockObject $listMock */
        $listMock = $this->createMock(ConstraintViolationList::class);
        $listMock->expects(static::once())->method('getIterator')->willReturn($iterator);

        $exception = new Http\ValidationException($listMock);

        static::assertSame(Response::HTTP_BAD_REQUEST, $exception->getHttpCode());
        static::assertSame([$propertyPath => $message], $exception->getData());
    }
}
