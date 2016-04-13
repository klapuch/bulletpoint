<?php
namespace Bulletpoint\Page;

use Bulletpoint\Exception;
use Bulletpoint\Component;
use Bulletpoint\Model\{
    Access, Constraint, Email
};
use Nette\Application\UI;
use Nette\Security;

final class AktivacePage extends BasePage {
    protected function createComponentReActivationForm() {
        return new Component\ReActivationForm($this->database);
    }

    public function actionAktivovat(string $code) {
        try {
            (new Constraint\VerificationCodeRule($this->database))
                ->isSatisfied($code);
            $owner = (new Access\MySqlVerificationCode(
                $code,
                $this->database
            ))->use()->owner();
            $this->flashMessage('Účet je aktivován', 'success');
            $this->user->login(
                new Security\Identity(
                    $owner->id(),
                    (string)$owner->role(),
                    ['username' => $owner->username()]
                )
            );
            $this->flashMessage('Jsi úspěšně přihlášen', 'success');
            $this->redirect('Default:');
        } catch(Exception\FormatException $ex) {
            $this->flashMessage($ex->getMessage(), 'danger');
            $this->redirect('Prihlasit:');
        } catch(Exception\ExistenceException $ex) {
            $this->flashMessage($ex->getMessage(), 'danger');
            $this->redirect('Prihlasit:');
        } catch(Exception\DuplicateException $ex) {
            $this->flashMessage($ex->getMessage(), 'danger');
            $this->redirect('Prihlasit:');
        }
    }
}