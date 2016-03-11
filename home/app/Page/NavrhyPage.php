<?php
namespace Bulletpoint\Page;

use Texy;
use Bulletpoint\Model\Paths;
use Bulletpoint\Model\{Access, Constraint, Wiki, Text};
use Bulletpoint\Core;
use Bulletpoint\Core\{Security, Filesystem, Storage};
use Bulletpoint\Exception;

final class NavrhyPage extends AdminBasePage {
	public function renderDefault() {
		$this->template->title = 'Návrhy';
	}

	public function renderDokumenty() {
		$this->template->title = 'Návrhy na dokumenty';
		$this->template->proposals = (new Wiki\MySqlDocumentProposals(
			$this->identity,
			$this->storage()
		))->iterate();
	}

	public function renderBulletpointy() {
		$this->template->title = 'Návrhy na bulletpointy';
		$this->template->proposals = (new Wiki\MySqlBulletpointProposals(
			$this->identity,
			$this->storage()
		))->iterate();
	}
}