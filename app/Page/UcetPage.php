<?php
namespace Bulletpoint\Page;

use Bulletpoint\Model\{
    User, Security, Filesystem, Paths
};
use Bulletpoint\Exception;
use Bulletpoint\Component;
use Nette\Application\UI;

final class UcetPage extends BasePage {
    /**
     * @var \Bulletpoint\Model\Security\AES256CBC @inject
     */
    public $cipher;
    /**
     * @var \Bulletpoint\Model\User\Account
     */
    private $account;

    public function startup() {
        parent::startup();
        $this->account = new User\Account(
            $this->identity,
            $this->database,
            $this->cipher
        );
    }

    public function renderDefault() {
        $this->template->username = $this->identity->username();
        $this->template->email = $this->account->email();
    }

    protected function createComponentPhotoForm() {
        return new Component\PhotoForm($this->identity);
    }

    protected function createComponentReEnterPasswordForm() {
        $form = new Component\ReEnterPasswordForm();
        $form->onSuccess[] = function(UI\Form $form) {
            $this->reEnterPasswordFormSucceeded($form);
        };
        return $form;
    }

    public function reEnterPasswordFormSucceeded(UI\Form $form) {
        $this->account->changePassword($form->values->passwords->password);
        $this->flashMessage('Heslo je změněno', 'success');
        $this->redirect('this');
    }

    protected function createComponentProfilePhoto() {
        return new Component\ProfilePhoto($this->identity);
    }
}