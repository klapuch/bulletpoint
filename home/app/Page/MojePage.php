<?php
namespace Bulletpoint\Page;

use Bulletpoint\Model\{Access, Constraint, Wiki};
use Bulletpoint\Core\Security;
use Bulletpoint\Exception;

final class MojePage extends BasePage {
	public function renderDokumenty() {
		$this->template->title = 'Dokumenty';
		$this->template->documents = (new Wiki\OwnedMySqlDocuments(
			$this->identity,
			$this->storage(),
			new Wiki\MySqlInformationSources($this->storage())
		))->iterate();
	}

	public function renderBulletpointy() {
		$this->template->title = 'Bulletpointy';
		$this->template->bulletpoints = (new Wiki\MySqlBulletpoints(
			$this->identity,
			$this->storage()
		))->byIdentity($this->identity);
	}
}