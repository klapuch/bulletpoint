<?php
namespace Bulletpoint\Page;

use Bulletpoint\Model\Wiki;
use Nette\Utils;

final class ProchazetPage extends BasePage {
    public function renderDokumenty($strana = 1) {
        $pagination = new Utils\Paginator;
        $pagination->itemsPerPage = 10;
        $pagination->page = $strana;
        $documents = (new Wiki\LimitedMySqlDocuments(
            $this->database,
            new Wiki\AllMySqlDocuments(
                $this->database,
                new class implements Wiki\Documents {
                    public function iterate(): \Iterator {  }
                    public function add(
                        string $title,
                        string $description,
                        Wiki\InformationSource $source
                    ): Wiki\Document {  }
                }
            ),
            $pagination
        ))->iterate();
        if(!$documents->valid() || $strana < $pagination->firstPage)
            $this->error('Strana neexistuje');
        $pagination->itemCount = $documents->key();
        $this->template->documents = $documents;
        $this->template->pagination = $pagination;
    }
}