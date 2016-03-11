<?php
namespace Bulletpoint\Page;

use Bulletpoint\Exception;

abstract class AdminBasePage extends BasePage {
	public function setUp() {
		parent::setUp();
		if(!$this->hasAccess('administrace')) {
			throw new Exception\AccessDeniedException(
				'Na tuto adresu nemáte přístup',
				self::ACCESS_DENIED
			);
		}
	}

	protected function beforeRender() {
		parent::beforeRender();
		$this->layout = 'adminLayout';
	}
}