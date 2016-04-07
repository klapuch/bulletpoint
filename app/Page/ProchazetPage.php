<?php
namespace Bulletpoint\Page;

use Bulletpoint\Model\Wiki;
use Bulletpoint\Component;
use Nette\Utils;

final class ProchazetPage extends BasePage {
    /**
     * @var \Nette\Utils\Paginator
     */
    private $pagination;
    /**
     * @var \Bulletpoint\Model\Wiki\LimitedMySqlDocuments
     */
    private $limitedDocuments;

    public function actionDokumenty() {
        $this->pagination = new Utils\Paginator;
        $this->pagination->itemsPerPage = 10;
        $this->limitedDocuments = new Wiki\LimitedMySqlDocuments(
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
            $this->pagination
        );
        $this->pagination->itemCount = $this->limitedDocuments->count();
    }

    public function renderDokumenty() {
        $this->template->documents = $this->limitedDocuments->iterate();
    }

    protected function createComponentPagination() {
        return new Component\Pagination(
            $this->pagination
        );
    }
}