<?php
namespace Bulletpoint\Page;

use Bulletpoint\Model\Wiki;
use Nette\Utils;

final class ProchazetPage extends BasePage {
    public function renderDokumenty($strana = 1) {
        $pagination = new Utils\Paginator;
        $pagination->page = $strana;
        $documents = (new Wiki\LimitedMySqlDocuments(
            $this->database,
            new Wiki\OwnedMySqlDocuments($this->identity, $this->database),
            $pagination
        ))->iterate();
        if(!$documents->valid() || $strana < $pagination->firstPage)
            $this->error('Strana neexistuje');
        $pagination->itemCount = $documents->key();
        $this->template->documents = $documents;
        $this->template->pagination = $pagination;
    }
}