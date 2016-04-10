<?php
namespace Bulletpoint\Page;

use Bulletpoint\Model\Wiki;
use Bulletpoint\Exception;

final class HledaniPage extends BasePage {
    public function renderDefault($keyword) {
        $this->template->keyword = $keyword;
        $documents = (new Wiki\SearchedMySqlDocuments(
            $keyword,
            $this->database,
            new class implements Wiki\Documents {
                public function iterate(): \Iterator {  }
                public function add(
                    string $title,
                    string $description,
                    Wiki\InformationSource $source
                ): Wiki\Document {  }
            }
        ))->iterate();
        $this->template->documents = $documents;
    }
}