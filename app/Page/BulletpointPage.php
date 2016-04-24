<?php
namespace Bulletpoint\Page;

use Bulletpoint\Model\{
    Constraint, Wiki, Translation, Rating, Security, Filesystem, Text, Storage
};
use Bulletpoint\Exception;
use Bulletpoint\Component;
use Nette\Http\IResponse;
use Nette\Application\UI;

final class BulletpointPage extends BasePage {
    /**
     * @var \Bulletpoint\Model\Wiki\Bulletpoint
     */
    private $bulletpoint;

    public function startup() {
        parent::startup();
        try {
            if(!isset($this->parameters['id']))
                return;
            $id = $this->parameters['id'];
            (new Constraint\BulletpointExistenceRule($this->database))
                ->isSatisfied($id);
            $this->bulletpoint = new Wiki\MySqlBulletpoint(
                $id,
                $this->database
            );
        } catch(Exception\ExistenceException $ex) {
            $this->error(
                $ex->getMessage(),
                IResponse::S404_NOT_FOUND
            );
        }
    }

    public function actionUpravit(int $id) {
        $this->template->bulletpoint = $bulletpoint = $this->bulletpoint;
        $this['editBulletpointForm']['bulletpoint']->defaults = [
            'content' => $bulletpoint->content(),
        ];
    }

    protected function createComponentEditBulletpointForm() {
        $form = new Component\BaseForm();
        $form->addComponent(
            (new Component\BulletpointContainer())->create(),
            'bulletpoint'
        );
        $form->onSuccess[] = function(UI\Form $form) {
            $this->editBulletpointFormSucceeded($form);
        };
        return $form;
    }

    public function editBulletpointFormSucceeded(UI\Form $form) {
        try {
            $bulletpoint = $this->bulletpoint;
            $bulletpoint->edit($form->values->bulletpoint->content);
            $this->flashMessage('Bulletpoint byl upraven', 'success');
            $this->redirect(
                'Dokument:',
                (string)new Translation\MySqlDocumentSlug(
                    $bulletpoint->document()->id(),
                    $this->database
                )
            );
        } catch(Exception\DuplicateException $ex) {
            $form->addError($ex->getMessage());
        }
    }

    public function actionPridat(string $slug) {
        try {
            (new Constraint\DocumentSlugExistenceRule($this->database))
                ->isSatisfied($slug);
        } catch(Exception\ExistenceException $ex) {
            $this->error(
                'Dokument neexistuje',
                IResponse::S404_NOT_FOUND
            );
        }
    }

    public function renderPridat(string $slug) {
        $this->template->document = new Wiki\MySqlDocument(
            (new Translation\MySqlDocumentSlug(
                $slug,
                $this->database
            ))->origin(),
            $this->database
        );
    }

    protected function createComponentAddBulletpointForm() {
        $form = new Component\BaseForm();
        $form->addComponent(
            (new Component\BulletpointContainer())->create(),
            'bulletpoint'
        );
        $form->addComponent(
            (new Component\InformationSourceContainer())->create(),
            'source'
        );
        $form->onSuccess[] = function(UI\Form $form) {
            $this->addBulletpointFormSucceeded($form);
        };
        return $form;
    }

    public function addBulletpointFormSucceeded(UI\Form $form) {
        try {
            $values = $form->values;
            (new Storage\Transaction($this->database))
                ->start(
                    function() use ($values) {
                        $source = $values->source;
                        (new Wiki\MySqlBulletpointProposals(
                            $this->identity,
                            $this->database
                        ))->propose(
                            new Wiki\MySqlDocument(
                                (new Translation\MySqlDocumentSlug(
                                    $this->getParameter('slug'),
                                    $this->database
                                ))->origin(),
                                $this->database
                            ),
                            $values->bulletpoint->content,
                            (new Wiki\MySqlInformationSources($this->database))
                                ->add(
                                    $source->place,
                                    $source->year,
                                    $source->author
                                )
                        );
                    }
                );
            $this->flashMessage(
                'Bulletpoint byl zaslán ke kontrole',
                'success'
            );
            $this->redirect('Dokument:', $this->getParameter('slug'));
        } catch(Exception\DuplicateException $ex) {
            $this->flashMessage($ex->getMessage(), 'danger');
        }
    }
}