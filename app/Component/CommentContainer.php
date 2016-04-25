<?php
namespace Bulletpoint\Component;

use Nette\Application\UI;

final class CommentContainer extends BaseContainer {
    protected function configure() {
        $this->addTextArea('content', 'Obsah')
            ->addRule(UI\Form::FILLED, '%label musí být vyplněn')
            ->addRule(UI\Form::MIN_LENGTH, '%label musí mít aspoň %d znaky', 2);
        $this->addSubmit('act');
    }
}
