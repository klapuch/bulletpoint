<?php
namespace Bulletpoint\Page;

use Bulletpoint\Model\{Constraint};
use Bulletpoint\Exception;

final class BanPage extends AdminBasePage {
	public function renderZrusit(int $id) {}
	public function actionZrusit(int $id) {
		try {
			$this->csrf->defend();
			(new Constraint\BanExistenceRule($this->storage()))
			->isSatisfied($id);
			(new Constraint\MySqlSin($id, $this->storage()))->forgive();
			$this->flashMessage->flash('Uživatel je odblokován', 'success');
			$this->response->redirectReferer('bany/prehled');
		} catch(Exception\CsrfException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
		} catch(Exception\ExistenceException $ex) {
			$this->response->redirect('chyba/404');
		} catch(\LogicException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
			$this->response->redirectReferer('bany/prehled');
		}
	}
}