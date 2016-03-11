<?php
namespace Bulletpoint\Page;

use Bulletpoint\Model\{Conversation, Constraint, Translation};
use Bulletpoint\Exception;

final class KomentarePage extends BasePage {
	public function renderPridat(string $slug) {}
	public function actionPridat(string $slug) {
		$this->template->slug = $slug;
		try {
			(new Constraint\DocumentSlugExistenceRule($this->storage()))
			->isSatisfied($slug);
			$this->template->documentId = (new Translation\MySqlDocumentSlug(
				$slug,
				$this->storage()
			))->origin();
		} catch(Exception\ExistenceException $ex) {
			$this->response->redirect('chyba/404');
		}
	}

	public function submitAddCommentForm($post) {
		try {
			$this->csrf->defend();
			(new Constraint\FillRule('Komentář musí být vyplněn'))
			->isSatisfied($post->content);
			(new Conversation\MySqlDiscussion(
				$this->template->documentId,
				$this->identity,
				$this->storage()
			))->contribute($post->content);
			$this->flashMessage->flash('Komentář byl přidán', 'success');
		} catch(Exception\FormatException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
			$this->response->redirect('dokument/zobrazit/' . $this->template->slug);
		} catch(Exception\CsrfException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
			$this->response->redirect('dokument/zobrazit/' . $this->template->slug);
		} finally {
			$this->response->redirect(
				'dokument/zobrazit/' . $this->template->slug . '#diskuze'
			);
		}
	}
}