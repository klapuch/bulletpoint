<?php
namespace Bulletpoint\Component;

use Bulletpoint\Model\{
    Access, Constraint, Email, Security, Storage
};
use Bulletpoint\Exception;
use Nette\Application\UI;

final class ReEnterPasswordForm extends BaseControl {
    public $onSuccess = [];

    public function render() {
        $this->template->setFile(__DIR__ . '/ReEnterPasswordForm.latte');
        $this->template->render();
    }

    protected function createComponentForm() {
        $form = new BaseForm();
        $form->addComponent(
            (new ReEnterPasswordContainer())->create(),
            'passwords'
        );
        $form->addSubmit('act');
        $form->onSuccess = $this->onSuccess;
        return $form;
    }
}
