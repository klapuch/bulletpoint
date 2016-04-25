<?php
namespace Bulletpoint\Component;

use Nette\Application\UI;

final class InformationSourceContainer extends BaseContainer {
    public function configure() {
        $this->addText('place', 'Místo');
        $this->addText('year', 'Rok')
            ->setType('number')
            ->addCondition(UI\Form::FILLED)
            ->addRule(UI\Form::INTEGER, '%label musí být celočíselný')
            ->addRule(
                UI\Form::RANGE,
                '%label musí od %d do %d',
                [0, date('Y')]
            );
        $this->addText('author', 'Autor');
        $this->addSubmit('act');
    }
}