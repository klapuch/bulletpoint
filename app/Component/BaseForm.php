<?php
namespace Bulletpoint\Component;

use Nette\Application\UI;

final class BaseForm extends UI\Form {
    public function fireEvents() {
        $this->onError[] = function() {
            $this->presenter->flashMessage(current($this->errors), 'danger');
        };
        parent::fireEvents();
    }
}