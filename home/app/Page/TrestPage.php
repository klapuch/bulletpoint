<?php
namespace Bulletpoint\Page;

use Bulletpoint\Model\{Constraint};
use Bulletpoint\Exception;

final class TrestPage extends AdminBasePage {
	public function renderZrusit(int $id) {}
	public function actionZrusit(int $id) {
		try {
			$this->csrf->defend();
			(new Constraint\PunishmentExistenceRule($this->storage()))
			->isSatisfied($id);
			(new Constraint\MySqlPunishment($id, $this->storage()))->forgive();
			$this->flashMessage->flash('Uživatel je odblokován', 'success');
			$this->response->redirectReferer('tresty/prehled');
		} catch(Exception\CsrfException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
		} catch(Exception\ExistenceException $ex) {
			$this->response->redirect('chyba/404');
		} catch(\LogicException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
			$this->response->redirectReferer('tresty/prehled');
		}
	}
}