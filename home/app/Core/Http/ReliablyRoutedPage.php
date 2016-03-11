<?php
namespace Bulletpoint\Core\Http;

use Bulletpoint\Core\Filesystem;

final class ReliablyRoutedPage implements RoutedPage {
	private $path;
	private $routedPage;

	public function __construct(
		Filesystem\Path $path,
		RoutedPage $routedPage
	) {
		$this->path = $path;
		$this->routedPage = $routedPage;
	}

	public function page(): string {
		if($this->pageExists())
			return $this->routedPage->page();
		throw new \RuntimeException(
			sprintf(
				'Page %s in %s does not exist',
				$this->routedPage->page(),
				$this->path()
			)
		);
	}

	private function pageExists(): bool {
		return is_file($this->path());
	}

	private function path(): string {
		return sprintf(
			$this->path->full(),
			$this->routedPage->page()
		);
	}
}