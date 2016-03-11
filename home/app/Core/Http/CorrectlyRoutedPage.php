<?php
namespace Bulletpoint\Core\Http;

use Bulletpoint\Core\Text;

final class CorrectlyRoutedPage implements RoutedPage {
	private $routedPage;
	private $correction;

	public function __construct(
		RoutedPage $routedPage,
		Text\Correction $correction
	) {
		$this->routedPage = $routedPage;
		$this->correction = $correction;
	}

	public function page(): string {
		return $this->correction->replacement(
			$this->routedPage->page()
		);
	}
}