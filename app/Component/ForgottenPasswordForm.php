<?php
namespace Bulletpoint\Component;

use Bulletpoint\Model\{
    Access, Constraint, Email, Security, Storage
};
use Bulletpoint\Exception;
use Nette\Application\UI;

final class ForgottenPasswordForm extends BaseControl {
    private $database;
    private $cipher;

    public function __construct(
        Storage\Database $database,
        Security\Cipher $cipher
    ) {
        parent::__construct();
        $this->database = $database;
        $this->cipher = $cipher;
    }

    public function createTemplate() {
        $template = parent::createTemplate();
        $template->captcha = $captcha = new Security\ImageCaptcha();
        $this->presenter->session->getSection('security')->captcha = serialize(
            $captcha
        );
        return $template;
    }

    public function render() {
        $this->template->setFile(__DIR__ . '/ForgottenPasswordForm.latte');
        $this->template->render();
    }

    protected function createComponentForm() {
        $form = new BaseForm();
        
        $form->addText('captcha')
            ->addRule(UI\Form::FILLED, 'Text musí být opsán');
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
        $form->onValidate[] = function(UI\Form $form) {
            try {
                unserialize(
                    $this->presenter->session->getSection('security')->captcha
                )->verify($form->values->captcha);
            } catch(Exception\AccessDeniedException $ex) {
                $form->addError($ex->getMessage());
            }
        };
        return $form;
    }

    protected function formSucceeded(UI\Form $form) {
        try {
            $values = $form->values;
            (new Storage\Transaction($this->database))
                ->start(
                    function() use ($values) {
                        (new Access\LimitedForgottenPasswords(
                            new Access\MySqlForgottenPasswords(
                                $this->database,
                                $this->cipher
                            ),
                            $this->database
                        ))->remind($values->email);
                        (new Email\MailService)->send(
                            new Email\MailMessage(
                                new Email\ForgottenPasswordMessage(
                                    $values->email,
                                    $this->database
                                )
                            )
                        );
                    }
                );
            $this->presenter->flashMessage(
                'Na email ti byly zaslány informace pro změnu hesla',
                'success'
            );
            $this->presenter->redirect('this');
        } catch(Exception\StorageException $ex) {
            $this->presenter->flashMessage($ex->getMessage(), 'danger');
        } catch(\OverflowException $ex) {
            $this->presenter->flashMessage($ex->getMessage(), 'danger');
        }
    }
}
