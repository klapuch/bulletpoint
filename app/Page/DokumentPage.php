<?php
namespace Bulletpoint\Page;

use Texy;
use Bulletpoint\Model\{
    User, Constraint, Conversation, Wiki, Text, Translation, Report, Rating, Security, Filesystem, Storage
};
use Bulletpoint\Exception;
use Bulletpoint\Component;
use Nette\Application\UI;
use Nette\Http\IResponse;
use Nette\Caching\Storages;
use Nette\Utils\Strings;

final class DokumentPage extends BasePage {
    /**
     * @var \Bulletpoint\Model\Wiki\Document
     */
    private $document;

    public function startup() {
        parent::startup();
        try {
            if(!isset($this->parameters['slug']))
                return;
            $slug = $this->parameters['slug'];
            (new Constraint\DocumentSlugExistenceRule($this->database))
                ->isSatisfied($slug);
            $id = (new Translation\MySqlDocumentSlug(
                $slug,
                $this->database
            ))->origin();
            $this->document = new Wiki\CachedDocument(
                new Wiki\MySqlDocument($id, $this->database),
                new Storages\MemoryStorage()
            );
        } catch(Exception\ExistenceException $ex) {
            $this->error(
                'Dokument neexistuje',
                IResponse::S404_NOT_FOUND
            );
        }
    }

    protected function createComponentInformationSource() {
        return new Component\InformationSource($this->document->source());
    }

    protected function createComponentBulletpoints() {
        return new Component\Bulletpoints(
            new Wiki\CachedBulletpoints(
                new Wiki\CategorizedMySqlBulletpoints(
                    $this->database,
                    $this->document,
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

    protected function createComponentDiscussion() {
        return new Component\Discussion(
            new Conversation\CachedDiscussion(
                new Conversation\MySqlDiscussion(
                    $this->document->id(),
                    $this->identity,
                    $this->database
                ),
                new Storages\MemoryStorage()
            ),
            $this->identity,
            $this->database
        );
    }

    public function renderDefault(string $slug) {
        $this->template->identity = $this->identity;
        $this->template->backlink = $this->storeRequest('+45 minutes');
        $this->template->document = $document = $this->document;
        $this->template->slug = $slug;
    }

    protected function createComponentNewDocumentForm() {
        $form = new Component\BaseForm();
        
        $form->addComponent(
            new Component\DocumentContainer,
            'document'
        );
        $form->addComponent(
            new Component\InformationSourceContainer,
            'source'
        );
        $form->onSuccess[] = function(UI\Form $form) {
            $this->newDocumentFormSucceeded($form);
        };
        return $form;
    }

    public function newDocumentFormSucceeded(UI\Form $form) {
        try {
            $values = $form->values;
            (new Storage\Transaction($this->database))
                ->start(
                    function() use ($values) {
                        $source = $values->source;
                        $document = $values->document;
                        (new Wiki\MySqlDocumentProposals(
                            $this->identity,
                            $this->database
                        ))->propose(
                            $document->title,
                            $document->description,
                            (new Wiki\MySqlInformationSources($this->database))
                                ->add(
                                    $source->place,
                                    $source->year,
                                    $source->author
                                )
                        );
                    }
                );
            $this->flashMessage('Dokument byl zaslán ke kontrole', 'success');
            $this->redirect('this');
        } catch(Exception\DuplicateException $ex) {
            $this->flashMessage($ex->getMessage(), 'danger');
            $this->flashMessage('Zkus upřesnit titulek', 'info');
        }
    }

    public function actionUpravit(string $slug) {
        $this->template->document = $document = $this->document;
        $this['editForm']['document']->defaults = [
            'title' => $document->title(),
            'description' => $document->description(),
        ];
    }

    protected function createComponentEditForm() {
        $form = new Component\BaseForm();
        
        $form->addComponent(
            new Component\DocumentContainer,
            'document'
        );
        $form->onSuccess[] = function(UI\Form $form) {
            $this->editFormSucceeded($form);
        };
        return $form;
    }

    public function editFormSucceeded(UI\Form $form) {
        try {
            $document = $this->document;
            $values = $form->values->document;
            $slug = (new Storage\Transaction($this->database))
                ->start(
                    function() use ($values, $document) {
                        $document->edit(
                            $values->title,
                            $values->description
                        );
                        return (new Translation\MySqlDocumentSlug(
                            $this->getParameter('slug'),
                            $this->database
                        ))->rename(Strings::webalize($values->title));
                    }
                );
            $this->flashMessage('Dokument byl upraven', 'success');
            $this->redirect('Dokument:', (string)$slug);
        } catch(Exception\DuplicateException $ex) {
            $this->flashMessage($ex->getMessage(), 'danger');
        }
    }

    protected function createComponentCommentForm() {
        $form = new Component\BaseForm();
        $form->addComponent(
            new Component\CommentContainer,
            'comment'
        );
        $form->onSuccess[] = function(UI\Form $form) {
            $this->commentFormSucceeded($form);
        };
        return $form;
    }

    public function commentFormSucceeded(UI\Form $form) {
        (new Conversation\MySqlDiscussion(
            $this->document->id(),
            $this->identity,
            $this->database
        ))->post($form->values->comment->content);
        $this->flashMessage('Komentář byl přidán', 'success');
        $this->redirect('this#diskuze');
    }
}