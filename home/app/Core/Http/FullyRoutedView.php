<?php
namespace Bulletpoint\Core\Http;

use Bulletpoint\Core\{Text, Filesystem};

final class FullyRoutedView implements RoutedView {
	private $path;
	private $routedView;
	private $routedPage;

	public function __construct(
		Filesystem\Path $path,
		RoutedView $routedView,
		RoutedPage $routedPage
	) {
		$this->path = $path;
		$this->routedView = $routedView;
		$this->routedPage = $routedPage;
	}

	public function view(): string {
		return sprintf(
			$this->path->full(),
			$this->routedPage->page() . '/' . $this->routedView->view()
		);
	}
}