<?php
namespace Bulletpoint\Fake;

use Bulletpoint\Core\Http;

final class RoutedPage implements Http\RoutedPage {
	private $page;

	public function __construct(string $page) {
		$this->page = $page;
	}

	public function page(): string {
		return $this->page;
	}
}