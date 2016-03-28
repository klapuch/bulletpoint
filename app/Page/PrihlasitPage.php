<?php
namespace Bulletpoint\Page;

use Bulletpoint\Component;
use Nette\Http\IResponse;
use Nette\Security;
use Bulletpoint\Model\Access;

final class PrihlasitPage extends BasePage {
    /** @persistent */
    public $backlink;

    public function actionDefault() {
        if($this->user->loggedIn) {
            $this->error(
                'Přihlášení pro člena neexistuje',
                IResponse::S404_NOT_FOUND
            );
        }
    }

    protected function createComponentLoginForm() {
        $form = new Component\LoginForm();
        $form->onSuccess[] = function(Security\Identity $identity) {
            if(!$this->isPunished(new Access\MySqlIdentity($identity->id, $this->database))) {
                $this->presenter->flashMessage(
                    'Jsi úspěšně přihlášen',
                    'success'
                );
            }
            $this->restoreRequest($this->backlink);
            $this->redirect('Default:');
        };
        return $form;
    }
}