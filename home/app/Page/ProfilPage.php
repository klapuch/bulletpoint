<?php
namespace Bulletpoint\Page;

use Bulletpoint\Model\Paths;
use Bulletpoint\Model\{Access, Constraint, User, Translation};
use Bulletpoint\Core\{Filesystem};
use Bulletpoint\Exception;

final class ProfilPage extends BasePage {
	private function profile(string $username): User\Profile {
		try {
			(new Constraint\ChainRule(
				new Constraint\UsernameRule,
				new Constraint\UsernameExistenceRule($this->storage())
			))->isSatisfied($username);
			return new User\MySqlProfile(
				new Access\MySqlIdentity(
					(new Translation\MySqlUsernameSlug(
						$username,
						$this->storage()
					))->origin(),
					$this->storage()
				),
				$this->storage()
			);
		} catch(Exception\ExistenceException $ex) {
			$this->response->redirect('chyba/404');
		}  catch(Exception\FormatException $ex) {
			$this->response->redirect('chyba/404');
		}
	}

	public function renderUzivatel(string $username) {
		$profile = $this->profile($username);
		$this->template->title = $username;
		$this->template->description = 'Veřejný profil uživatele ' . $username;
		$this->template->username = $username;
		$this->template->comments = $profile->comments();
		$this->template->bulletpoints = $profile->bulletpoints();
		$this->template->documents = $profile->documents();
		$owner = $profile->owner();
		$this->template->photo = (new User\ProfilePhoto(
			$owner,
			new Filesystem\Folder(Paths::profileImage())
		))->show()->asFile()->location();
		$this->template->owner = $owner;
		$this->template->ban = (new Constraint\MySqlBans(
			$this->identity,
			$this->storage()
		))->byIdentity($owner);
		$czechRoles = [
			'member' => 'Člen',
			'administrator' => 'Administrátor',
			'creator' => 'Tvůrce'
		];
		$this->template->role = $czechRoles[(string)$owner->role()];
	}

	public function renderPovysit(string $username) {}
	public function actionPovysit(string $username) {
		try {
			$this->csrf->defend();
			(new Access\RestrictedRole(
				$this->identity,
				$this->profile($username)->owner()->role()
			))->promote();
			$this->flashMessage->flash('Uživatel byl povýšen', 'success');
		} catch(Exception\CsrfException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
		} catch(\OverflowException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
		} catch(Exception\AccessDeniedException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
		} catch(Exception\StorageException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
		} finally {
			$this->response->redirect('profil/uzivatel/' . $username);
		}
	}

	public function renderDegradovat(string $username) {}
	public function actionDegradovat(string $username) {
		try {
			$this->csrf->defend();
			(new Access\RestrictedRole(
				$this->identity,
				$this->profile($username)->owner()->role()
			))->degrade();
			$this->flashMessage->flash('Uživatel byl degradován', 'success');
		} catch(Exception\CsrfException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
		} catch(\UnderflowException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
		} catch(Exception\AccessDeniedException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
		} catch(Exception\StorageException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
		} finally {
			$this->response->redirect('profil/uzivatel/' . $username);
		}
	}
}