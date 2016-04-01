<?php
namespace Bulletpoint\Component;

use Bulletpoint\Model\{
    Conversation, User, Filesystem, Access, Report, Storage, Text
};
use Texy;

final class Comment extends BaseControl {
    private $comment;
    private $complaints;
    private $identity;
    private $database;

    public function __construct(
        Conversation\Comment $comment,
        Report\Complaints $complaints,
        Access\Identity $identity,
        Storage\Database $database
    ) {
        parent::__construct();
        $this->comment = $comment;
        $this->complaints = $complaints;
        $this->identity = $identity;
        $this->database = $database;
    }

    public function render() {
        $this->template->setFile(__DIR__ . '/Comment.latte');
        $this->template->comment = $this->comment;
        $this->template->identity = $this->identity;
        if($this->presenter->user->loggedIn) {
            $this->template->complained = $this->complaints->iterate(
                new Report\Target($this->comment->id())
            )->valid();
        }
        $this->template->backlink = $this->presenter->storeRequest('+ 45 minutes');
        $this->template->render();
    }

    /**
     * @secured
     */
    public function handleSmazat() {
        $this->comment->erase();
        $this->presenter->flashMessage('Komentář je smazán', 'success');
        $this->presenter->redirect('this');
    }

    /**
     * @secured
     */
    public function handleStezovat(string $reason) {
        try {
            $this->complaints->complain(
                new Report\Target($this->comment->id()),
                $reason
            );
            if(!$this->presenter->isAjax()) {
                $this->presenter->flashMessage(
                    'Stížnost byla zaznamenána',
                    'success'
                );
                $this->presenter->redirect('this');
            }
            $this->redrawControl();
        } catch(\OverflowException $ex) {
            $this->presenter->flashMessage($ex->getMessage(), 'danger');
            $this->presenter->redirect('this');
        }
    }

    protected function createComponentProfilePhoto() {
        return new ProfilePhoto($this->comment->author());
    }
}