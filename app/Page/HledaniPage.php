<?php
namespace Bulletpoint\Page;

use Bulletpoint\Model\Wiki;
use Bulletpoint\Exception;
use Nette\Utils;

final class HledaniPage extends BasePage {
    public function renderDefault($keyword) {
        $this->template->keyword = $keyword;
        $documents = new Wiki\SearchedMySqlDocuments(
            $keyword,
            $this->database,
            new class implements Wiki\Documents {
                public function iterate(): \Iterator {  }
                public function count(): int {  }
                public function add(
                    string $title,
                    string $description,
                    Wiki\InformationSource $source
                ): Wiki\Document {  }
            }
        );
        $count = $this->template->count = $documents->count();
        if($count === 1) {
            $this->redirect(
                'Dokument:',
                Utils\Strings::webalize(
                    $documents->iterate()->current()->title()
                )
            );
        } elseif($count > 1) {
            $this->template->documents = $documents->iterate();
        }
    }
}