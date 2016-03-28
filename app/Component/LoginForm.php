<?php
namespace Bulletpoint\Component;

use Nette\Application\UI;
use Nette\Security\AuthenticationException;

final class LoginForm extends BaseControl {
    public $onSuccess = [];

    public function render() {
        $this->template->setFile(__DIR__ . '/LoginForm.latte');
        $this->template->render();
    }

    protected function createComponentForm() {
        $form = new BaseForm();
        $form->addProtection();
        $form->addText('username', 'Přezdívka')
            ->addRule(UI\Form::FILLED, '%label musí být vyplněna');
        $form->addPassword('password', 'Heslo')
            ->addRule(UI\Form::FILLED, '%label musí být vyplněno');
        $form->addCheckbox('permanent')
            ->setDefaultValue(false);
        $form->addSubmit('enter');
        $form->onSuccess[] = function(UI\Form $form) {
            $this->formSucceeded($form);
        };
        return $form;
    }

    protected function formSucceeded(UI\Form $form) {
        try {
            $values = $form->values;
            if($values->permanent)
                $this->presenter->user->setExpiration('14 days', false, true);
            $this->presenter->user->login($values->username, $values->password);
            $this->presenter->session->regenerateId();
            $this->onSuccess($this->presenter->user->identity);
        } catch(AuthenticationException $ex) {
            $form->addError($ex->getMessage());
        }
    }
}
