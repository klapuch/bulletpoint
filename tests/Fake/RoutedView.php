<?php
namespace Bulletpoint\Fake;

use Bulletpoint\Core\Http;

final class RoutedView implements Http\RoutedView {
	private $view;

	public function __construct(string $view) {
		$this->view = $view;
	}

	public function view(): string {
		return $this->view;
	}
}