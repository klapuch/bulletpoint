<?php
namespace Bulletpoint\Core\Reflection;

use Bulletpoint\Core\Http;

final class Request {
	private $post;
	private $routedView;
	private $reflectionClass;

	public function __construct(
		array $post,
		Http\RoutedView $routedView,
		\ReflectionClass $reflectionClass
	) {
		$this->post = $post;
		$this->routedView = $routedView;
		$this->reflectionClass = $reflectionClass;
	}

	public function submitForm(): string {
		return $this->post(array_map(function($singlePost) {
			return 'submit' . ucfirst($singlePost) . 'Form';
		}, array_keys($this->post)));
	}

	public function render(array $parameters): string {
		return $this->get(
			'render' . $this->routedView->view(),
			$parameters
		);
	}

	public function action(array $parameters): string {
		try {
			return $this->get(
				'action' . $this->routedView->view(),
				$parameters
			);
		} catch(\RuntimeException $ex) {
			return '';
		}
	}

	private function post(array $methods): string {
		if(!$this->canChoose($methods) && count($methods) > 0)
			$this->notFound($methods);
		return current($this->choices($methods));
	}

	private function get(string $method, array $parameters): string {
		if(!$this->canChoose($method))
			$this->notFound($method);
		$this->countParameters($method, $parameters);
		return current($this->choices($method))->name;
	}

	private function canChoose($requests): int {
		return count($this->choices($requests)) > 0;
	}

	private function choices($request) {
		if((array)$request === $request) { // is_array
			return array_filter(
				$request,
				function($action) {
					return !empty($this->choices($action));
			});
		}
		return array_filter(
			$this->availableMethods(),
			function($method) use ($request) {
				return $method->name === $request;
		});
	}

	private function availableMethods(): array {
		return $this->reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC);
	}

	private function notFound($method) {
		throw new \RuntimeException(
			sprintf(
				'Method %s in %s class does not exist',
				implode(', ', (array)$method),
				$this->reflectionClass->getName()
			)
		);
	}

	private function countParameters(string $currentMethod, array $parameters) {
		$method = current($this->choices($currentMethod));
		$match = $method->getNumberOfRequiredParameters() === count($parameters)
		|| $method->getNumberOfParameters() === count($parameters);
		if($match === false) {
			throw new \RuntimeException(
				sprintf(
					'Method %s needs more or less parameters',
					$currentMethod
				)
			);
		}
	}
}