<?php
namespace Bulletpoint\Page;

final class ChybaPage extends BasePage {
	public function renderDefault() {
		$this->template->title = '404 - Stránka nenalezena';
		header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
	}

	public function render404() {
		$this->template->title = '404 - Stránka nenalezena';
		header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
	}

	public function render503() {
		$this->template->title = '503 - Chyba na straně úložiště';
		header($_SERVER['SERVER_PROTOCOL'] . ' 503 Service Unavailable');
	}

	public function render403() {
		$this->template->title = '403 - Přístup odepřen';
		header($_SERVER['SERVER_PROTOCOL'] . ' 403 Site Access Denied');
	}
}