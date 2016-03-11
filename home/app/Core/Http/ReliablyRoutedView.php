<?php
namespace Bulletpoint\Core\Http;

use Bulletpoint\Core\{Text, Filesystem};

final class ReliablyRoutedView implements RoutedView {
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
		if($this->viewExists())
			return $this->routedView->view();
		throw new \RuntimeException(
			sprintf(
				'View %s in %s does not exist',
				$this->routedView->view(),
				$this->path()
			)
		);
	}

	private function viewExists(): bool {
		return is_file($this->path());
	}

	private function path(): string {
		return sprintf(
			$this->path->full(),
			$this->routedPage->page() . '/' . $this->routedView->view()
		);
	}
}