<?php
namespace Bulletpoint\Page;

use Bulletpoint\Model\Wiki;

final class ProchazetPage extends BasePage {
    public function renderDokumenty() {
        $this->template->documents = (new Wiki\AllMySqlDocuments(
            $this->database,
            new Wiki\OwnedMySqlDocuments($this->identity, $this->database)
        ))->iterate();
    }
}