<?php
namespace Bulletpoint\Page;

use Bulletpoint\Model\{
    Constraint, Wiki
};
use Bulletpoint\Exception;
use Bulletpoint\Component;
use Nette\Http\IResponse;
use Nette\Application\UI;

final class NavrhnutyDokumentPage extends BasePage {
    /**
     * @var \Bulletpoint\Model\Wiki\DocumentProposal
     */
    private $proposal;

    public function startup() {
        parent::startup();
        try {
            (new Constraint\DocumentProposalExistenceRule($this->database))
                ->isSatisfied($this->getParameter('id'));
            $this->proposal = new Wiki\MySqlDocumentProposal(
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

    public function renderUpravit(int $id) {
        $this->template->proposal = $this->proposal;
    }

    public function renderDefault(int $id) {
        $this->template->proposal = $this->proposal;
    }

    public function actionUpravit(int $id) {
        $this['editProposalForm']['document']->defaults = [
            'title' => $this->proposal->title(),
            'description' => $this->proposal->description(),
        ];
        $this->template->proposal = $this->proposal;
    }

    protected function createComponentEditProposalForm() {
        $form = new Component\BaseForm();
        $form->addProtection();
        $form->addComponent(
            (new Component\DocumentContainer())->create(),
            'document'
        );
        $form->onSuccess[] = function(UI\Form $form) {
            $this->editProposalFormSucceeded($form);
        };
        return $form;
    }

    public function editProposalFormSucceeded(UI\Form $form) {
        try {
            $values = $form->values->document;
            $this->proposal->edit($values->title, $values->description);
            $this->flashMessage('Návrh byl upraven', 'success');
            $this->redirect('NavrhnutyDokument:', $this->getParameter('id'));
        } catch(Exception\DuplicateException $ex) {
            $this->flashMessage($ex->getMessage(), 'danger');
        }
    }

    protected function createComponentDocumentProposal() {
        return new Component\DocumentProposal(
            $this->proposal,
            $this->database
        );
    }
}