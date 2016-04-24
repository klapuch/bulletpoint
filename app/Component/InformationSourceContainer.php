<?php
namespace Bulletpoint\Component;

use Nette\Application\UI;
use Nette\Forms\Container;

final class InformationSourceContainer {
    public function create(): Container {
        $container = new Container();
        $container->addText('place', 'Místo');
        $container->addText('year', 'Rok')
            ->setType('number')
            ->addCondition(UI\Form::FILLED)
            ->addRule(UI\Form::INTEGER, '%label musí být celočíselný')
            ->addRule(
                UI\Form::RANGE,
                '%label musí od %d do %d',
                [0, date('Y')]
            );
        $container->addText('author', 'Autor');
        $container->addSubmit('act');
        return $container;
    }
}