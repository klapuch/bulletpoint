<?php
namespace Bulletpoint\Page;

use Texy;
use Bulletpoint\Model\Paths;
use Bulletpoint\Model\{User, Wiki, Conversation, Constraint, Report, Text};
use Bulletpoint\Core\{Security, Filesystem};
use Bulletpoint\Exception;

final class StiznostPage extends AdminBasePage {
	private function comment(int $id): Conversation\Comment {
		try {
			(new Constraint\CommentExistenceRule(
				$this->storage()
			))->isSatisfied($id);
			return new Conversation\MySqlComment(
				$id,
				$this->identity,
				$this->storage()
			);
		} catch(Exception\ExistenceException $ex) {
			$this->response->redirect('chyba/404');
		}
	}

	public function actionDetail(int $id) {
		$this->template->comment = $this->comment($id);
		$complaints = (new Report\MySqlTarget(
			$this->template->comment->id(),
			$this->identity,
			$this->storage()
		))->complaints();
		if(!$complaints)
			$this->response->redirect('stiznosti');
		$this->template->complaints = $complaints;
	}

	public function renderDetail(int $id) {
		$this->template->title = 'Detail stížnosti';
		$this->template->photo = (new User\ProfilePhoto(
			$this->template->comment->author(),
			new Filesystem\Folder(Paths::profileImage())
		))->show()->asFile()->location();
		$this->template->publishingFormat = new Text\PublishingFormat(new Texy);
	}

	private function complaint(int $id): Report\Complaint {
		try {
			(new Constraint\ComplaintExistenceRule(
				$this->storage()
			))->isSatisfied($id);
			return new Report\MySqlComplaint(
				$id,
				$this->identity,
				$this->storage()
			);
		} catch(Exception\ExistenceException $ex) {
			$this->response->redirect('chyba/404');
		}
	}

	public function renderVyresit(int $id) {}
	public function actionVyresit(int $id) {
		$complaint = $this->complaint($id);
		try {
			$this->csrf->defend();
			$complaint->settle();
			$this->flashMessage->flash('Vyřešeno', 'success');
			$this->response->redirect('stiznosti');
		} catch(Exception\CsrfException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');	
		} finally {
			$this->response->redirect(
				'stiznost/detail/' . $complaint->target()->id()
			);
		}
	}
}