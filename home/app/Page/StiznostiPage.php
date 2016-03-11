<?php
namespace Bulletpoint\Page;

use Bulletpoint\Model\Paths;
use Bulletpoint\Model\{Conversation, Constraint, Report};
use Bulletpoint\Core\{Security, Filesystem, Storage};
use Bulletpoint\Exception;

final class StiznostiPage extends AdminBasePage {
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

	public function renderDefault() {
		$this->template->title = 'Stížnosti na komentáře';
		$this->template->complaints = (new Report\MySqlComplaints(
			$this->identity,
			$this->storage()
		))->iterate();
	}


	public function renderSmazat(int $id) {}
	public function actionSmazat(int $id) {
		try {
			$this->csrf->defend();
			$comment = $this->comment($id);
			(new Storage\Transaction($this->storage()))
			->start(function() use($comment) {
				$comment->erase();
				(new Report\MySqlComplaints(
					$this->identity,
					$this->storage()
				))->settle(new Report\MySqlTarget(
					$comment->id(),
					$this->identity,
					$this->storage()
				));
			});
			$this->flashMessage->flash('Komentář byl smazán', 'success');
		} catch(Exception\CsrfException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
		} catch(Exception\StorageException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
		} finally {
			$this->response->redirect('stiznosti');
		}
	}

	public function renderVyresit(int $id) {}
	public function actionVyresit(int $id) {
		try {
			$this->csrf->defend();
			$comment = $this->comment($id);
			(new Report\MySqlComplaints(
				$this->identity,
				$this->storage()
			))->settle(new Report\MySqlTarget(
				$comment->id(),
				$this->identity,
				$this->storage()
			));
			$this->flashMessage->flash('Komentář byl vyřešen', 'success');
		} catch(Exception\CsrfException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');	
		} finally {
			$this->response->redirect('stiznosti');
		}
	}
}