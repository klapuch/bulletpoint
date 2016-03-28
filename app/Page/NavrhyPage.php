<?php
namespace Bulletpoint\Page;

use Bulletpoint\Model\Wiki;

final class NavrhyPage extends BasePage {
    public function renderDefault() {
        $this->template->title = 'Návrhy';
    }

    public function renderDokumenty() {
        $this->template->proposals = (new Wiki\MySqlDocumentProposals(
            $this->identity,
            $this->database
        ))->iterate();
    }

    public function renderBulletpointy() {
        $this->template->proposals = (new Wiki\MySqlBulletpointProposals(
            $this->identity,
            $this->database
        ))->iterate();
    }
}