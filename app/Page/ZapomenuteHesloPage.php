<?php
namespace Bulletpoint\Page;

use Bulletpoint\Model\{
    Access, Constraint, Storage
};
use Bulletpoint\Exception;
use Bulletpoint\Component;
use Nette\Application\UI;
use Nette\Http\IResponse;

final class ZapomenuteHesloPage extends BasePage {
    /** @inject @var \Bulletpoint\Model\Security\AES256CBC */
    public $cipher;

    public function createComponentForgottenPasswordForm() {
        return new Component\ForgottenPasswordForm(
            $this->database,
            $this->cipher
        );
    }

    public function actionReset(string $reminder) {
        $this->template->reminder = $this->reminder();
    }

    protected function createComponentReEnterPasswordForm() {
        $form = new Component\ReEnterPasswordForm();
        $form->onSuccess[] = function(UI\Form $form) {
            $this->reEnterPasswordFormSucceeded($form);
        };
        return $form;
    }

    public function reEnterPasswordFormSucceeded(UI\Form $form) {
        try {
            $values = $form->values;
            $reminder = $this->reminder();
            (new Storage\Transaction($this->database))
                ->start(
                    function() use ($values, $reminder) {
                        (new Access\MySqlRemindedPassword(
                            $reminder,
                            $this->database,
                            $this->cipher
                        ))->change($values->passwords->password);
                    }
                );
            $this->flashMessage('Heslo bylo změněno', 'success');
            $this->redirect('Prihlasit:');
        } catch(Exception\StorageException $ex) {
            $this->flashMessage($ex->getMessage(), 'danger');
        }
    }

    private function reminder(): string {
        try {
            (new Constraint\ReminderRule(
                $this->database
            ))->isSatisfied($this->getParameter('reminder'));
            return $this->getParameter('reminder');
        } catch(Exception\FormatException $ex) {
            $this->error(
                $ex->getMessage(),
                IResponse::S404_NOT_FOUND
            );
        } catch(Exception\ExistenceException $ex) {
            $this->error(
                $ex->getMessage(),
                IResponse::S404_NOT_FOUND
            );
        } catch(Exception\DuplicateException $ex) {
            $this->error(
                $ex->getMessage(),
                IResponse::S404_NOT_FOUND
            );
        }
    }
}