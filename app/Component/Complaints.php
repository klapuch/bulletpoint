<?php
namespace Bulletpoint\Component;

use Bulletpoint\Exception;
use Bulletpoint\Model\{
    Access, Storage, Report, Conversation
};

final class Complaints extends BaseControl {
    private $target;
    private $identity;
    private $database;

    public function __construct(
        Report\Target $target,
        Access\Identity $identity,
        Storage\Database $database
    ) {
        parent::__construct();
        $this->target = $target;
        $this->identity = $identity;
        $this->database = $database;
    }

    public function render() {
        $this->template->setFile(__DIR__ . '/Complaints.latte');
        $this->template->complaints = (new Report\MySqlUnsettledComplaints(
            $this->identity,
            $this->database
        ))->iterate($this->target);
        $this->template->render();
    }

    /**
     * @secured
     */
    public function handleSmazat() {
        try {
            (new Storage\Transaction($this->database))
                ->start(
                    function() {
                        (new Conversation\MySqlComment(
                            $this->target->id(),
                            $this->identity,
                            $this->database
                        ))->erase();
                        (new Report\MySqlUnsettledComplaints(
                            $this->identity,
                            $this->database
                        ))->settle($this->target);
                    }
                );
            $this->flashMessage('Komentář byl smazán', 'success');
        } catch(Exception\StorageException $ex) {
            $this->flashMessage($ex->getMessage(), 'danger');
        } finally {
            $this->redirect('this');
        }
    }

    /**
     * @secured
     */
    public function handleVyresit(int $id) {
        (new Report\MySqlComplaint(
            $id,
            $this->identity,
            $this->database
        ))->settle();
        $this->flashMessage('Vyřešeno', 'success');
        $this->redirect('this');
    }

    /**
     * @secured
     */
    public function handleVyresitVse() {
        (new Report\MySqlUnsettledComplaints(
            $this->identity,
            $this->database
        ))->settle($this->target);
        $this->flashMessage('Stiznost byla vyřešena', 'success');
        $this->redirect('this');
    }
}