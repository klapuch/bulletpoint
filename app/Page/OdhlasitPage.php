<?php
namespace Bulletpoint\Page;

use Nette\Http\IResponse;

final class OdhlasitPage extends BasePage {
    public function actionDefault() {
        if(!$this->user->loggedIn) {
            $this->error(
                'Odhlášení pro hosta neexistuje',
                IResponse::S404_NOT_FOUND
            );
        }
        $this->user->logout(true);
        $this->session->regenerateId();
        $this->flashMessage('Jsi odhlášen', 'success');
        $this->redirect('Default:');
    }
}