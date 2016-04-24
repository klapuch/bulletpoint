<?php
namespace Bulletpoint\Page;

use Bulletpoint\Model\{
    Report, Constraint, Text, Conversation
};
use Bulletpoint\Exception;
use Nette\Application\UI;
use Nette\Http\IResponse;
use Bulletpoint\Component;

final class KomentarPage extends BasePage {
    /** @persistent */
    public $backlink;

    private function comment(): Conversation\Comment {
        try {
            (new Constraint\CommentExistenceRule(
                $this->database
            ))->isSatisfied($this->getParameter('id'));
            return new Conversation\MySqlComment(
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

    public function actionUpravit(int $id) {
        $comment = $this->comment();
        if($comment->author()->id() !== $this->identity->id() || !$comment->visible()) {
            $this->error(
                'Komentář nemůžeš upravovat',
                IResponse::S404_NOT_FOUND
            );
        }
        $this['editCommentForm']['comment']->defaults = [
            'content' => $comment->content(),
        ];
    }

    protected function createComponentEditCommentForm() {
        $form = new Component\BaseForm();
        $form->addComponent(
            (new Component\CommentContainer())->create(),
            'comment'
        );
        $form->onSuccess[] = function(UI\Form $form) {
            $this->editCommentFormSucceeded($form);
        };
        return $form;
    }

    public function editCommentFormSucceeded(UI\Form $form) {
        $this->comment()->edit($form->values->comment->content);
        $this->flashMessage('Komentář byl upraven', 'success');
        $this->restoreRequest($this->backlink);
        $this->redirect('this');
    }
}