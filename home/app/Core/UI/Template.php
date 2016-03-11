<?php
namespace Bulletpoint\Core\UI;

interface Template {
	public function addFilter(string $name, $callback);
	public function render(string $contour);
}