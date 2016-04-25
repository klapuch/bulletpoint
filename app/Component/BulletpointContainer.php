<?php
namespace Bulletpoint\Component;

use Nette\Application\UI;

final class BulletpointContainer extends BaseContainer {
    public function configure() {
        $this->addText('content', 'Obsah')
            ->addRule(UI\Form::FILLED, '%label musí být vyplněn');
        $this->addSubmit('act');
    }
}