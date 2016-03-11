<?php
namespace Bulletpoint\Page;

final class FrontPage extends Page {
	public function load() {
		$pageName = $this->router->page();
		$page = new $pageName(
			$this->request,
			$this->template,
			$this->router,
			$this->reflectedRequest,
			$this->configuration
		);
		$page->load();
	}
}