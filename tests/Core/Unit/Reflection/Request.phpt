<?php

/**
* @testCase
* @phpVersion > 7.0.0
*/

namespace Bulletpoint\Core\Unit;

use Tester;
use Tester\Assert;
use Bulletpoint\TestCase;
use Bulletpoint\Fake;
use Bulletpoint\Core\Reflection;

require __DIR__ . '/../../../bootstrap.php';

final class Request extends TestCase\Mockery {
	private $reflectionClass;

	protected function setUp() {
		parent::setUp();
		$this->reflectionClass = $this->mockery('ReflectionClass');
		$this->reflectionClass->shouldReceive('getName')->andReturn('Bulletpoint\Core\Unit\SomePage');
	}

	/**
	* @throws RuntimeException Method renderUnknownMethod in Bulletpoint\Core\Unit\SomePage class does not exist
	*/
	public function testNotFoundGet() {
		$this->reflectionClass->shouldReceive('getMethods')
		->andReturn([new \ReflectionMethod('Bulletpoint\Core\Unit\SomePage', 'renderDefault')]);
		$httpMethod = new Reflection\Request(
			[],
			new Fake\RoutedView('UnknownMethod'),
			$this->reflectionClass
		);
		$httpMethod->render([]);
	}

	/**
	* @throws RuntimeException Method renderDefault needs more or less parameters
	*/
	public function testInvalidParameterCount() {
		$this->reflectionClass->shouldReceive('getMethods')
		->andReturn([new \ReflectionMethod('Bulletpoint\Core\Unit\SomePage', 'renderDefault')]);
		$httpMethod = new Reflection\Request(
			[],
			new Fake\RoutedView('Default'),
			$this->reflectionClass
		);
		$httpMethod->render([]);
	}

	public function testValidGet() {
		$this->reflectionClass->shouldReceive('getMethods')
		->andReturn([new \ReflectionMethod('Bulletpoint\Core\Unit\SomePage', 'renderDefault')]);
		$httpMethod = new Reflection\Request(
			[],
			new Fake\RoutedView('Default'),
			$this->reflectionClass
		);
		Assert::same($httpMethod->render(['x', 'y', 'z']), 'renderDefault');
	}

	public function testValidPost() {
		$this->reflectionClass->shouldReceive('getMethods')
		->andReturn([new \ReflectionMethod('Bulletpoint\Core\Unit\SomePage', 'actionDefault')]);
		$httpMethod = new Reflection\Request(
			[],
			new Fake\RoutedView('Default'),
			$this->reflectionClass
		);
		Assert::same($httpMethod->action(['a', 'b', 'c']), 'actionDefault');
	}

	public function testNotFoundPost() {
		$this->reflectionClass->shouldReceive('getMethods')
		->andReturn([new \ReflectionMethod('Bulletpoint\Core\Unit\SomePage', 'actionDefault')]);
		$httpMethod = new Reflection\Request(
			['unknown' => 'someValue', 'post2' => 'someValue2'],
			new Fake\RoutedView(''),
			$this->reflectionClass
		);
		Assert::same($httpMethod->action([]), '');
	}

	public function testSubmittingForm() {
		$this->reflectionClass->shouldReceive('getMethods')
		->andReturn([new \ReflectionMethod('Bulletpoint\Core\Unit\SomePage', 'submitMyForm')]);
		$httpMethod = new Reflection\Request(
			['unknown' => 'someValue', 'post2' => 'someValue2', 'my' => 'value'],
			new Fake\RoutedView(''),
			$this->reflectionClass
		);
		Assert::same($httpMethod->submitForm(), 'submitMyForm');
	}

	/**
	* @throws RuntimeException Method submitUnknownForm, submitPost2Form in Bulletpoint\Core\Unit\SomePage class does not exist
	*/
	public function testSubmittingUnknownForm() {
		$this->reflectionClass->shouldReceive('getMethods')
		->andReturn([new \ReflectionMethod('Bulletpoint\Core\Unit\SomePage', 'submitMyForm')]);
		$httpMethod = new Reflection\Request(
			['Unknown' => 'someValue', 'Post2' => 'someValue2'],
			new Fake\RoutedView(''),
			$this->reflectionClass
		);
		$httpMethod->submitForm();
	}
}

class SomePage {
	public function renderDefault($a, $b, $c) {}
	public function renderSomething() {}
	public function actionDefault($a, $b, $c) {}
	public function actionSomething() {}
	public function submitMyForm($post) {}
}


(new Request())->run();
