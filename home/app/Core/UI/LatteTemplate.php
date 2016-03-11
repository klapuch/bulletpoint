<?php
namespace Bulletpoint\Core\UI;

use Bulletpoint\Core\{Filesystem, Text, Http};
use Latte;

final class LatteTemplate implements Template {
	private $parameters = [];
	private $routedView;
	private $latte;
	private $path;

	public function __construct(
		Filesystem\Path $path,
		Latte\Engine $latte,
		Http\RoutedView $routedView
	) {
		$this->path = $path;
		$this->latte = $latte;
		$this->routedView = $routedView;
	}

	public function __set(string $name, $value) {
		$this->parameters[$name] = $value;
	}

	public function __get(string $name) {
		return $this->parameters[$name];
	}

	public function addFilter(string $name, $callback) {
		$this->latte->addFilter($name, $callback);
	}

	public function render(string $layout) {
		$this->latte->addFilter('renderView', function() {
			$this->latte->render($this->routedView->view(), $this->parameters);
		});
		$this->latte->render(
			sprintf($this->path->full(), $layout),
			$this->parameters
		);
	}
}