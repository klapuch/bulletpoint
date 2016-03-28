<?php
namespace Bulletpoint\Component;

use Nette\Application\UI;
use Nette\Forms\Container;

final class BulletpointContainer {
    public function create(): Container {
        $container = new Container();
        $container->addText('content', 'Obsah')
            ->addRule(UI\Form::FILLED, '%label musí být vyplněn');
        $container->addSubmit('act');
        return $container;
    }
}