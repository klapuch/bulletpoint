<?php
namespace Bulletpoint\Component;

use Nette\Application\UI;

final class DocumentContainer extends BaseContainer {
    public function configure() {
        $this->addText('title', 'Titulek')
            ->addRule(UI\Form::FILLED, '%label musí být vyplněn');
        $this->addTextArea('description', 'Popis')
            ->addRule(UI\Form::FILLED, '%label musí být vyplněn');
        $this->addSubmit('act');
    }
}