<?php
namespace Bulletpoint\Page;

use Bulletpoint\Model\Paths;
use Bulletpoint\Model\{Access, Constraint, Wiki};
use Bulletpoint\Core\{Security, Filesystem};
use Bulletpoint\Exception;

final class ZdrojPage extends BasePage {
	public function actionUpravit(int $id) {
		try {
			(new Constraint\InformationSourceExistenceRule($this->storage()))
			->isSatisfied($id);
			$this->template->source = new Wiki\MySqlInformationSource(
				$id,
				$this->storage()
			);
		} catch(Exception\ExistenceException $ex) {
			$this->response->redirect('chyba/404');
		}
		$this->template->id = $id;
		$this->template->referer = $_SERVER['HTTP_REFERER'] ?? null;
	}

	public function renderUpravit(int $id) {
		$this->template->title = 'Úprava zdroje';
	}

	public function submitEditSourceForm($post) {
		try {
			$this->csrf->defend();
			(new Constraint\YearRule)->isSatisfied($post->year);
			$this->template->source->edit(
				$post->place,
				$post->year,
				$post->author
			);
			$this->flashMessage->flash('Zdroj byl upraven', 'success');
			if($post->referer)
				$this->response->redirectUrl($post->referer);
			$this->response->redirect();
		} catch(Exception\FormatException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
		} catch(Exception\CsrfException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
		}
	}
}