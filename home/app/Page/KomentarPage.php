<?php
namespace Bulletpoint\Page;

use Bulletpoint\Model\{Report, Constraint, Text, Conversation};
use Bulletpoint\Exception;

final class KomentarPage extends BasePage {
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

	public function actionUpravit(int $id) {
		$comment = $this->comment($id);
		if($comment->author()->id() !== $this->identity->id()
		|| !$comment->visible()) {
			$this->flashMessage->flash(
				'Tento komentář nemůžeš upravovat',
				'danger'
			);
			$this->response->redirect('chyba/404');
		}
		$this->template->comment = $comment;
		$this->template->id = $id;
		$this->template->referer = $_SERVER['HTTP_REFERER'] ?? null;
	}

	public function renderUpravit(int $id) {
		$this->template->title = 'Úprava komentáře';
		$this->template->content = $this->template->comment->content();
	}

	public function submitEditCommentForm($post) {
		try {
			$this->csrf->defend();
			(new Constraint\FillRule('Komentář musí být vyplněn'))
			->isSatisfied($post->content);
			$this->template->comment->edit($post->content);
			$this->flashMessage->flash('Komentář byl upraven', 'success');
			if($post->referer)
				$this->response->redirectUrl($post->referer);
			$this->response->redirect();
		} catch(Exception\FormatException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
		} catch(Exception\CsrfException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
		}
	}

	public function renderSmazat(int $id) {}
	public function actionSmazat(int $id) {
		try {
			$this->csrf->defend();
			$this->comment($id)->erase();
			$this->flashMessage->flash('Komentář byl smazán', 'success');
		} catch(Exception\CsrfException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
		} catch(\LogicException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
		} finally {
			$this->response->redirectReferer();
		}
	}

	public function renderStezovat(int $id, string $reason) {}
	public function actionStezovat(int $id, string $reason) {
		try {
			$this->csrf->defend();
			(new Report\AllowedComplaints(
				new Report\MySqlComplaints(
					$this->identity,
					$this->storage()
			)))->complain(
				new Report\MySqlTarget(
					$this->comment($id)->id(),
					$this->identity,
					$this->storage()
				),
				$reason
			);
			$this->flashMessage->flash('Stížnost byla zaznamenána', 'success');
		} catch(Exception\CsrfException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
		} catch(\OverflowException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'warning');
		} finally {
			$this->response->redirectReferer();
		}
	}
}