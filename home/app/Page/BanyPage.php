<?php
namespace Bulletpoint\Page;

use Bulletpoint\Model\{Access, Constraint};
use Bulletpoint\Exception;

final class BanyPage extends AdminBasePage {
	public function renderPrehled() {
		$this->template->title = 'Přehled banů';
		$this->template->bans = (new Constraint\MySqlBans(
			$this->identity,
			$this->storage()
		))->iterate();
	}

	public function renderZablokovat(int $sinner) {}
	public function actionZablokovat(int $sinner) {
		$this->template->sinner = $sinner;
	}

	public function submitBanUserForm($post) {
		$sinner = new Access\MySqlIdentity(
			$this->template->sinner,
			$this->storage()
		);
		try {
			$this->csrf->defend();
			(new Constraint\MySqlBans($this->identity, $this->storage()))
			->give(
				$sinner,
				new \DateTime($post->expiration),
				$post->reason
			);
			$this->flashMessage->flash('Uživatel je zablokován', 'success');
		} catch(Exception\CsrfException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
		} catch(\LogicException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
		} finally {
			$this->response->redirect(
				'profil/uzivatel/' . $sinner->username()
			);
		}
	}
}