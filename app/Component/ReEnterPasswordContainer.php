<?php
namespace Bulletpoint\Component;

use Nette\Application\UI;

final class ReEnterPasswordContainer extends BaseContainer {
    protected function configure() {
        $this->addPassword('password', 'Heslo')
            ->addRule(UI\Form::FILLED, '%label musí být vyplněno')
            ->addRule(UI\Form::MIN_LENGTH, '%label musí mít aspoň %d znaků', 6);
        $this->addPassword('repeatedPassword', 'Heslo znovu')
            ->addRule(UI\Form::EQUAL, 'Hesla se neshodují', $this['password']);
    }
}