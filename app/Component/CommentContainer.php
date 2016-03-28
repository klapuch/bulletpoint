<?php
namespace Bulletpoint\Component;

use Nette\Application\UI;
use Nette\Forms\Container;

final class CommentContainer {
    public function create(): Container {
        $container = new Container();
        $container->addTextArea('content', 'Obsah')
            ->addRule(UI\Form::FILLED, '%label musí být vyplněn')
            ->addRule(UI\Form::MIN_LENGTH, '%label musí mít aspoň %d znaky', 2);
        $container->addSubmit('act');
        return $container;
    }
}
