<?php
namespace Bulletpoint\Component;

use Nette\Application\UI;

final class BaseForm extends UI\Form {
    public function addError($message) {
        $this->presenter->flashMessage($message, 'danger');
        parent::addError($message);
    }
}