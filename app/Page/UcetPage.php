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

    protected function createComponentPhotoChangeForm() {
        $form = new Component\BaseForm();
        $form->addProtection();
        $form->addUpload('photo', 'Soubor')
            ->addRule(UI\Form::FILLED, '%label musí být vybrán')
            ->addRule(UI\Form::MAX_FILE_SIZE, 'Fotka nesmí přesahovat 500 kB.', 500 * 1024);
        $form->onSuccess[] = function() {
            $this->photoChangeFormSucceeded();
        };
        $form->addSubmit('change');
        return $form;
    }

    public function photoChangeFormSucceeded() {
        try {
            (new User\ProfilePhoto(
                $this->identity,
                new Filesystem\Folder(Paths::profileImage())
            ))->change(
                new Filesystem\Image(
                    new Filesystem\UploadedFile($_FILES['photo'])
                )
            );
            $this->flashMessage('Fotka je změněna', 'success');
            $this->flashMessage('Obnov stránku pro změnu', 'warning');
            $this->redirect('this');
        } catch(Exception\UploadException $ex) {
            $this->flashMessage($ex->getMessage(), 'danger');
        } catch(Exception\StorageException $ex) {
            $this->flashMessage($ex->getMessage(), 'danger');
        }
    }

    protected function createComponentPasswordChangeForm() {
        $form = new Component\BaseForm();
        $form->addProtection();
        $form->addComponent(
            (new Component\ReEnterPasswordContainer())->create(),
            'passwords'
        );
        $form->addSubmit('change');
        $form->onSuccess[] = function(UI\Form $form) {
            $this->passwordChangeFormSucceeded($form);
        };
        return $form;
    }

    public function passwordChangeFormSucceeded(UI\Form $form) {
        $this->account->changePassword($form->values->passwords->password);
        $this->flashMessage('Heslo je změněno', 'success');
        $this->redirect('this');
    }

    protected function createComponentProfilePhoto() {
        return new Component\ProfilePhoto($this->identity);
    }
}