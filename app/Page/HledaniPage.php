<?php
namespace Bulletpoint\Page;

use Bulletpoint\Model\Wiki;
use Bulletpoint\Exception;

final class HledaniPage extends BasePage {
    public function renderDefault() {
        $keyword = $this->template->keyword = trim(
            $this->getParameter('keyword')
        );
        $matches = (new Wiki\Fulltext($this->database))->matches($keyword);
        $this->template->count = key($matches);
        $this->template->matches = current($matches);
        if(key($matches) === 1) {
            $this->redirect(
                'Dokument:',
                $matches[1][0]['slug']
            );
        }
    }
}