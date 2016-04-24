<?php
namespace Bulletpoint\Component;

use Nette\Application\UI;
use Bulletpoint\Model\{
    Access, Email, User, Security, Constraint, Storage
};
use Bulletpoint\Exception;

final class RegistrationForm extends BaseControl {
    private $cipher;
    private $database;

    public function __construct(
        Storage\Database $database,
        Security\Cipher $cipher
    ) {
        parent::__construct();
        $this->cipher = $cipher;
        $this->database = $database;
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
        $this->template->setFile(__DIR__ . '/RegistrationForm.latte');
        $this->template->render();
    }

    protected function createComponentForm() {
        $form = new BaseForm();
        $form->addText('captcha')
            ->addRule(UI\Form::FILLED, 'Text musí být opsán');
        $form->addText('username', 'Přezdívka')
            ->addRule(UI\Form::FILLED, '%label musí být vyplněna')
            ->addRule(
                UI\Form::PATTERN,
                '%label se musí skládat z kombinace číslic a písmen od 3 do 30 znaků',
                '[a-zA-Z0-9]{3,30}'
            )->addRule(
                function($control) {
                    try {
                        (new Constraint\UsernameExistenceRule($this->database))
                            ->isSatisfied($control->value);
                        return false;
                    } catch(Exception\ExistenceException $ex) {
                        return true;
                    }
                },
                'Přezdívka již existuje'
            );
        $form->addText('email', 'Email')
            ->setType('email')
            ->addRule(UI\Form::FILLED, '%label musí být vyplněn')
            ->addRule(UI\Form::EMAIL, '"%value" není email')
            ->addRule(
                function($control) {
                    try {
                        (new Constraint\EmailExistenceRule($this->database))
                            ->isSatisfied($control->value);
                        return false;
                    } catch(Exception\ExistenceException $ex) {
                        return true;
                    }
                },
                'Email již existuje'
            );
        $form->addComponent(
            (new ReEnterPasswordContainer())->create(),
            'passwords'
        );
        $form->addSubmit('register');
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
                        (new Access\Registration(
                            $this->database,
                            $this->cipher
                        ))->register(
                            new User\Applicant(
                                $values->username,
                                $values->passwords->password,
                                $values->email
                            )
                        );
                        (new Access\MySqlVerificationCodes($this->database))
                            ->generate($values->email);
                        (new Email\MailService)->send(
                            new Email\MailMessage(
                                new Email\ActivationMessage(
                                    $values->email,
                                    $this->database
                                )
                            )
                        );
                    }
                );
            $this->presenter->flashMessage(
                'Jsi úspěšně registrován',
                'success'
            );
            $this->presenter->flashMessage(
                'Na uvedený email ti byl zaslán aktivační kód',
                'warning'
            );
            $this->presenter->redirect('this');
        } catch(Exception\StorageException $ex) {
            $this->presenter->flashMessage($ex->getMessage(), 'danger');
        }
    }
}
