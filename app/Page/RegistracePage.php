<?php
namespace Bulletpoint\Page;

use Nette\Http\IResponse;
use Nette\Application\UI;
use Bulletpoint\Component;

final class RegistracePage extends BasePage {
    /** @inject @var \Bulletpoint\Model\Security\AES256CBC */
    public $cipher;

    public function actionDefault() {
        if($this->user->loggedIn) {
            $this->error(
                'Registrace pro člena neexistuje',
                IResponse::S404_NOT_FOUND
            );
        }
    }

    public function createComponentRegistrationForm() {
        return new Component\RegistrationForm($this->database, $this->cipher);
    }
}