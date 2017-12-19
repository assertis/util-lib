<?php
declare(strict_types=1);

namespace Assertis\Util\Input;

use Assertis\Util\Http\ValidationException;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @author Rafał Orłowski <rafal.orlowski@assertis.co.uk>
 */
class InputFactoryTest extends TestCase
{
    /**
     * @throws ValidationException
     */
    public function testFromArrayThrowsExceptionOnValidationErrors()
    {
        $data = ['foo' => 'bar'];

        /** @var ValidatorInterface|PHPUnit_Framework_MockObject_MockObject $validator */
        $validator = $this->createMock(ValidatorInterface::class);
        $constraint = $this->createMock(Constraint::class);

        $errors = $this->createMock(ConstraintViolationListInterface::class);
        $errors->method('count')->willReturn(1);

        TestInput::$constraint = $constraint;

        $validator->expects(self::once())
            ->method('validate')
            ->with($data, $constraint)
            ->willReturn($errors);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Input data did not pass validation');

        $factory = new InputFactory($validator);
        $factory->fromArray(TestInput::class, $data);
    }

    /**
     * @throws ValidationException
     */
    public function testFromArraySuccess()
    {
        $data = ['foo' => 'bar'];

        $constraint = $this->createMock(Constraint::class);
        $errors = $this->createMock(ConstraintViolationListInterface::class);
        $errors->method('count')->willReturn(0);
        /** @var ValidatorInterface|PHPUnit_Framework_MockObject_MockObject $validator */
        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects(self::once())->method('validate')->with($data, $constraint)->willReturn($errors);

        TestInput::$constraint = $constraint;

        $factory = new InputFactory($validator);
        $input = $factory->fromArray(TestInput::class, $data);
        
        self::assertInstanceOf(TestInput::class, $input);
    }
}
