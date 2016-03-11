<?php
namespace Bulletpoint\Page;

use Bulletpoint\Core\{Control, UI, Reflection, Http};

abstract class Page {
	protected $layout = 'layout';
	protected $request;
	protected $template;
	protected $router;
	protected $reflectedRequest;
	protected $configuration;

	public function __construct(
		Http\Request $request,
		UI\Template $template,
		Http\Router $router,
		Reflection\Request $reflectedRequest,
		Control\Configuration $configuration
	) {
		$this->request = $request;
		$this->template = $template;
		$this->router = $router;
		$this->reflectedRequest = $reflectedRequest;
		$this->configuration = $configuration;
	}
	
	public function load() {
		$this->setUp();
		$this->makeRequest();
		$this->beforeRender();
		$this->renderTemplate();
	}

	protected function setUp() { /** Override */ }

	private function makeRequest() {
		$this->action();
		$this->submitForm();
		$this->render();
	}

	protected function beforeRender() { /** Override */ }

	private function renderTemplate() {
		$this->template->render($this->layout);
	}

	private function action() {
		$action = $this->reflectedRequest->action($this->router->parameters());
		if($action)
			$this->$action(...$this->router->parameters());
	}

	private function submitForm() {
		$submittedForm = $this->reflectedRequest->submitForm();
		if($submittedForm) {
			$this->$submittedForm((object)$this->request->post());
			$this->renewFormData();
		}
	}

	private function renewFormData() {
		foreach($this->request->post() as $name => $value)
			$this->template->$name = $value;
	}

	private function render() {
		$get = $this->reflectedRequest->render($this->router->parameters());
		$this->$get(...$this->router->parameters());
	}
}