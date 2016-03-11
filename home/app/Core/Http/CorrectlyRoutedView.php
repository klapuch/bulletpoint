<?php
namespace Bulletpoint\Core\Http;

use Bulletpoint\Core\Text;

final class CorrectlyRoutedView implements RoutedView {
	private $routedView;
	private $correction;

	public function __construct(
		RoutedView $routedView,
		Text\Correction $correction
	) {
		$this->routedView = $routedView;
		$this->correction = $correction;
	}

	public function view(): string {
		return $this->correction->replacement(
			$this->routedView->view()
		);
	}
}