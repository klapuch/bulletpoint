<?php
namespace Bulletpoint\Component;

use Bulletpoint\Model\{
    Conversation, Access, Storage, Report
};
use Nette\Application\UI;
use Nette\Caching\Storages;

final class Discussion extends BaseControl {
    private $discussion;
    private $identity;
    private $database;

    public function __construct(
        Conversation\Discussion $discussion,
        Access\Identity $identity,
        Storage\Database $database
    ) {
        parent::__construct();
        $this->discussion = $discussion;
        $this->identity = $identity;
        $this->database = $database;
    }

    public function render() {
        $this->template->setFile(__DIR__ . '/Discussion.latte');
        $this->template->comments = $this->discussion->comments();
        $this->template->render();
    }

    protected function createComponentDiscussion() {
        $components = [];
        foreach($this->discussion->comments() as $comment) {
            $components[$comment->id()] = new Comment(
                $comment,
                new Report\AllowedComplaints(
                    new Report\MySqlCriticComplaints(
                        $this->identity,
                        $this->database,
                        new Report\MySqlUnsettledComplaints(
                            $this->identity,
                            $this->database
                        )
                    )
                ),
                $this->identity,
                $this->database
            );
        }
        return new UI\Multiplier(
            function($id) use ($components) {
                return $components[$id];
            }
        );
    }
}

