<?php
namespace Bulletpoint\Component;

use Nette\Forms\Container;
use Nette\Application\UI;

final class ReEnterPasswordContainer {
    public function create(): Container {
        $container = new Container();
        $container->addPassword('password', 'Heslo')
            ->addRule(UI\Form::FILLED, '%label musí být vyplněno')
            ->addRule(UI\Form::MIN_LENGTH, '%label musí mít aspoň %d znaků', 6);
        $container->addPassword('repeatedPassword', 'Heslo znovu')
            ->addRule(
                UI\Form::EQUAL,
                'Hesla se neshodují',
                $container['password']
            );
        return $container;
    }
}