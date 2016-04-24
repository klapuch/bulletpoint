<?php
namespace Bulletpoint\Component;

use Bulletpoint\Exception;
use Bulletpoint\Model\{
    Access, Constraint, Email, Storage
};
use Nette\Application\UI;

final class ReActivationForm extends BaseControl {
    private $database;

    public function __construct(Storage\Database $database) {
        parent::__construct();
        $this->database = $database;
    }

    public function render() {
        $this->template->setFile(__DIR__ . '/ReActivationForm.latte');
        $this->template->render();
    }

    protected function createComponentForm() {
        $form = new BaseForm();
        $form->addText('email', 'Email')
            ->setType('email')
            ->addRule(UI\Form::FILLED, '%label musí být vyplněn')
            ->addRule(UI\Form::EMAIL, '"%value" není email')
            ->addRule(
                function($control) {
                    try {
                        (new Constraint\EmailExistenceRule($this->database))
                            ->isSatisfied($control->value);
                        return true;
                    } catch(Exception\ExistenceException $ex) {
                        return false;
                    }
                },
                'Email neexistuje'
            );
        $form->addSubmit('send');
        $form->onSuccess[] = function(UI\Form $form) {
            $this->formSucceeded($form);
        };
        return $form;
    }

    protected function formSucceeded(UI\Form $form) {
        try {
            $email = $form->values->email;
            (new Access\ReserveVerificationCodes(
                $this->database
            ))->generate($email);
            (new Email\MailService)->send(
                new Email\MailMessage(
                    new Email\ActivationMessage(
                        $email,
                        $this->database
                    )
                )
            );
            $this->presenter->flashMessage(
                'Ověřovací kód byl zaslán',
                'success'
            );
            $this->presenter->redirect('this');
        } catch(Exception\ExistenceException $ex) {
            $this->presenter->flashMessage($ex->getMessage(), 'danger');
        }
    }
}
