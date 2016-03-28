<?php
namespace Bulletpoint\Component;

use Nette\Application\UI;
use Nette\Forms\Container;

final class DocumentContainer {
    public function create(): Container {
        $container = new Container();
        $container->addText('title', 'Titulek')
            ->addRule(UI\Form::FILLED, '%label musí být vyplněn');
        $container->addTextArea('description', 'Popis')
            ->addRule(UI\Form::FILLED, '%label musí být vyplněn');
        $container->addSubmit('act');
        return $container;
    }
}