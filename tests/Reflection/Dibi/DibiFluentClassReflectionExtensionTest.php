<?php declare(strict_types = 1);

namespace PHPStan\Reflection\Dibi;

use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Type\VerbosityLevel;

class DibiFluentClassReflectionExtensionTest extends \PHPStan\Testing\PHPStanTestCase
{

	/** @var \PHPStan\Broker\Broker */
	private $broker;

	/** @var \PHPStan\Reflection\Dibi\DibiFluentClassReflectionExtension */
	private $extension;

	protected function setUp(): void
	{
		$this->broker = $this->createBroker();
		$this->extension = new DibiFluentClassReflectionExtension();
	}

	/**
	 * @return array<array{class-string, bool}>
	 */
	public function dataHasMethod(): array
	{
		return [
			[
				\Dibi\Fluent::class,
				true,
			],
			[
				\stdClass::class,
				false,
			],
		];
	}

	/**
	 * @dataProvider dataHasMethod
	 * @param string $className
	 * @param bool $result
	 */
	public function testHasMethod(string $className, bool $result): void
	{
		$classReflection = $this->broker->getClass($className);
		self::assertSame($result, $this->extension->hasMethod($classReflection, 'select'));
	}

	public function testGetMethod(): void
	{
		$classReflection = $this->broker->getClass(\Dibi\Fluent::class);
		$methodReflection = $this->extension->getMethod($classReflection, 'select');
		$parametersAcceptor = ParametersAcceptorSelector::selectSingle($methodReflection->getVariants());
		self::assertSame('select', $methodReflection->getName());
		self::assertSame($classReflection, $methodReflection->getDeclaringClass());
		self::assertFalse($methodReflection->isStatic());
		self::assertEmpty($parametersAcceptor->getParameters());
		self::assertTrue($parametersAcceptor->isVariadic());
		self::assertFalse($methodReflection->isPrivate());
		self::assertTrue($methodReflection->isPublic());
		self::assertSame(\Dibi\Fluent::class, $parametersAcceptor->getReturnType()->describe(VerbosityLevel::value()));
	}

}
