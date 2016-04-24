<?php
namespace Bulletpoint\Page;

use Bulletpoint\Model\{
    Constraint, Wiki, Storage
};
use Bulletpoint\Exception;
use Bulletpoint\Component;
use Nette\Http\IResponse;
use Nette\Application\UI;
use Nette\Caching\Storages;

final class NavrhnutyBulletpointPage extends BasePage {
    protected function createComponentBulletpoints() {
        return new Component\Bulletpoints(
            new Wiki\CachedBulletpoints(
                new Wiki\CategorizedMySqlBulletpoints(
                    $this->database,
                    $this->proposal()->document(),
                    new Wiki\OwnedMySqlBulletpoints(
                        $this->identity,
                        $this->database
                    )
                ),
                new Storages\MemoryStorage
            ),
            $this->identity,
            $this->database
        );
    }

    protected function createComponentBulletpointProposal() {
        return new Component\BulletpointProposal(
            $this->proposal(),
            $this->database
        );
    }

    public function renderDefault(int $id) {
        $this->template->document = $this->proposal()->document();
    }

    public function renderUpravit(int $id) {
        $this->template->document = $this->proposal()->document();
    }

    public function actionUpravit(int $id) {
        $this['editProposalForm']['bulletpoint']->defaults = [
            'content' => $this->proposal()->content(),
        ];
    }

    public function createComponentEditProposalForm() {
        $form = new Component\BaseForm();
        $form->addComponent(
            (new Component\BulletpointContainer())->create(),
            'bulletpoint'
        );
        $form->onSuccess[] = function(UI\Form $form) {
            $this->editProposalFormSucceeded($form);
        };
        return $form;
    }

    public function editProposalFormSucceeded(UI\Form $form) {
        try {
            $this->proposal()->edit($form->values->bulletpoint->content);
            $this->flashMessage('Návrh byl upraven', 'success');
            $this->redirect('NavrhnutyBulletpoint:', $this->getParameter('id'));
        } catch(Exception\DuplicateException $ex) {
            $this->flashMessage($ex->getMessage(), 'danger');
        }
    }

    private function proposal(): Wiki\BulletpointProposal {
        try {
            (new Constraint\BulletpointProposalExistenceRule($this->database))
                ->isSatisfied($this->getParameter('id'));
            return new Wiki\MySqlBulletpointProposal(
                $this->getParameter('id'),
                $this->identity,
                $this->database
            );
        } catch(Exception\ExistenceException $ex) {
            $this->error(
                $ex->getMessage(),
                IResponse::S404_NOT_FOUND
            );
        }
    }
}