<?php
namespace Bulletpoint\Page;

use Bulletpoint\Model\Wiki;

final class MojePage extends BasePage {
	public function renderDokumenty() {
		$this->template->documents = (new Wiki\OwnedMySqlDocuments(
			$this->identity,
			$this->database
		))->iterate();
	}

	public function renderBulletpointy() {
		$this->template->bulletpoints = (new Wiki\OwnedMySqlBulletpoints(
			$this->identity,
			$this->database
		))->iterate();
	}
}