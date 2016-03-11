<?php
namespace Bulletpoint\Core\Http;

final class BasicRouter implements Router {
	private $pageRouter;
	private $viewRouter;
	private $parameterRouter;

	public function __construct(
		RoutedPage $pageRouter,
		RoutedView $viewRouter,
		RoutedParameter $parameterRouter
	) {
		$this->pageRouter = $pageRouter;
		$this->viewRouter = $viewRouter;
		$this->parameterRouter = $parameterRouter;
	}

	public function page(): string {
		return $this->pageRouter->page();
	}

	public function view(): string {
		return $this->viewRouter->view();
	}

	public function parameters(): array {
		return $this->parameterRouter->parameters();
	}
}