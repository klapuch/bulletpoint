<?php
namespace Bulletpoint\Component;

use Bulletpoint\Model\{
    User, Filesystem, Paths, Access
};
use Bulletpoint\Exception;
use Nette\Application\UI;

final class PhotoForm extends BaseControl {
    private $identity;
    
    public function __construct(Access\Identity $identity) {
        parent::__construct();
        $this->identity = $identity;
    }

    public $onSuccess = [];

    public function render() {
        $this->template->setFile(__DIR__ . '/PhotoForm.latte');
        $this->template->render();
    }

    protected function createComponentForm() {
        $form = new BaseForm();
        
        $form->addUpload('photo', 'Soubor')
            ->addRule(UI\Form::FILLED, '%label musí být vybrán')
            ->addRule(UI\Form::MAX_FILE_SIZE, 'Fotka nesmí přesahovat 500 kB.', 500 * 1024);
        $form->onSuccess[] = function() {
            $this->formSucceeded();
        };
        $form->addSubmit('act');
        return $form;
    }

    protected function formSucceeded() {
        try {
            (new User\ProfilePhoto(
                $this->identity,
                new Filesystem\Folder(Paths::profileImage())
            ))->change(
                new Filesystem\Image(
                    new Filesystem\UploadedFile($_FILES['photo'])
                )
            );
            $this->presenter->flashMessage('Fotka je změněna', 'success');
            $this->presenter->flashMessage('Obnov stránku pro změnu', 'warning');
            $this->presenter->redirect('this');
        } catch(Exception\UploadException $ex) {
            $this->presenter->flashMessage($ex->getMessage(), 'danger');
        } catch(Exception\StorageException $ex) {
            $this->presenter->flashMessage($ex->getMessage(), 'danger');
        }
    }
}
