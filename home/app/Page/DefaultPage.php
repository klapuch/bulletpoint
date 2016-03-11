<?php
namespace Bulletpoint\Page;

final class DefaultPage extends BasePage {
	public function renderDefault() {
		$this->template->title = 'bulletpoint';
		$this->template->description = 'Rychlý, snadný a pohodlný způsob jak získat informace ve srozumitelné a krátké formě v podobě odrážkového systému';
	}
}