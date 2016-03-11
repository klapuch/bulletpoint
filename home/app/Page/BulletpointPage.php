<?php
namespace Bulletpoint\Page;

use Bulletpoint\Model\Paths;
use Bulletpoint\Model\{Constraint, Wiki, Translation, Rating};
use Bulletpoint\Core\{Security, Filesystem, Text};
use Bulletpoint\Exception;

final class BulletpointPage extends BasePage {
	private function bulletpoint(int $id): Wiki\Bulletpoint {
		try {
			(new Constraint\BulletpointExistenceRule($this->storage()))
			->isSatisfied($id);
			return new Wiki\MySqlBulletpoint(
				$id,
				$this->storage()
			);
		} catch(Exception\ExistenceException $ex) {
			$this->response->redirect('chyba/404');
		}
	}
	public function actionUpravit(int $id) {
		$this->template->bulletpoint = $this->bulletpoint($id);
		$this->template->id = $id;
	}

	public function renderUpravit(int $id) {
		$this->template->title = 'Úpráva bulletpointu k dokumentu';
	}

	public function submitEditBulletpointForm($post) {
		try {
			$this->csrf->defend();
			(new Constraint\FillRule('Obsah musí být vyplněn'))
			->isSatisfied($post->content);
			$this->template->bulletpoint->edit($post->content);
			$this->flashMessage->flash('Bulletpoint byl upraven', 'success');
			$this->response->redirect(
				'dokument/zobrazit/' . (string)new Translation\MySqlDocumentSlug(
					$this->template->bulletpoint->document()->id(),
					$this->storage()
				) . '#bulletpointy'
			);
		} catch(Exception\CsrfException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');	
		} catch(Exception\FormatException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');	
		}
	}

	public function renderPro(int $id) {}
	public function actionPro(int $id) {
		$this->rate($id, '+');
	}

	public function renderProti(int $id) {}
	public function actionProti(int $id) {
		$this->rate($id, '-');
	}

	private function rate(int $id, string $type) {
		$bulletpoint = $this->bulletpoint($id);
		$slug = (string)new Translation\MySqlDocumentSlug(
			$bulletpoint->document()->id(),
			$this->storage()
		);
		try {
			$this->csrf->defend();
			$rating = new Rating\MySqlBulletpointRating(
				$bulletpoint,
				$this->identity,
				$this->storage()
			);
			$type === '+' ? $rating->increment() : $rating->decrement();
			$this->flashMessage->flash('Ohodnoceno', 'success');
			$this->response->redirect(
				'dokument/zobrazit/' . $slug . '#bulletpointy'
			);
		} catch(Exception\CsrfException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
		} finally {
			$this->response->redirect('dokument/zobrazit/' . $slug);
		}
	}

	public function actionPridat(string $slug) {
		try {
			(new Constraint\DocumentSlugExistenceRule($this->storage()))
			->isSatisfied($slug);
			$this->template->id = (new Translation\MySqlDocumentSlug(
				$slug,
				$this->storage()
			))->origin();
			$this->template->slug = $slug;
		} catch(Exception\ExistenceException $ex) {
			$this->response->redirect('chyba/404');
		}
	}

	public function renderPridat(string $slug) {
		$this->template->title = (new Wiki\MySqlDocument(
			$this->template->id,
			$this->storage()
		))->title();
	}

	public function submitAddBulletpointForm($post) {
		try {
			$this->csrf->defend();
			(new Constraint\FillRule('Obsah musí být vyplněn'))
			->isSatisfied($post->content);
			(new Constraint\YearRule)->isSatisfied($post->year);
			(new Wiki\MySqlBulletpointProposals(
				$this->identity,
				$this->storage()
			))->propose(
				new Wiki\MySqlDocument(
					$this->template->id,
					$this->storage()
				),
				$post->content,
				(new Wiki\MySqlInformationSources($this->storage()))
				->create($post->place, $post->year, $post->author)
			);
			$this->flashMessage->flash('Bulletpoint byl zaslán ke kontrole', 'success');
			$this->response->redirect('dokument/zobrazit/' . $this->template->slug);
		} catch(Exception\FormatException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
		} catch(Exception\CsrfException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
		} catch(Exception\DuplicateException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
		}
	}
}