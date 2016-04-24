<?php
namespace Bulletpoint\Page;

use Texy;
use Bulletpoint\Model\{
    Conversation, Constraint, Report
};
use Bulletpoint\Exception;
use Bulletpoint\Component;
use Nette\Http\IResponse;

final class StiznostPage extends BasePage {
    public function actionDefault(int $id) {
        $complaints = (new Report\MySqlUnsettledComplaints(
            $this->identity,
            $this->database
        ))->iterate(new Report\Target($this->comment()->id()));
        if(!$complaints->valid())
            $this->redirect('Stiznosti:');
    }

    protected function createComponentComment() {
        return new Component\Comment(
            $this->comment(),
            new Report\AllowedComplaints(
                new Report\MySqlComplainerComplaints(
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

    protected function createComponentComplaints() {
        return new Component\Complaints(
            new Report\Target($this->comment()->id()),
            $this->identity,
            $this->database
        );
    }

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
}