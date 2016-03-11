<?php
namespace Bulletpoint\Page;

use Bulletpoint\Model\{Access, Constraint, User};
use Bulletpoint\Core\Security;
use Bulletpoint\Exception;

final class PrihlasitPage extends BasePage {
	public function actionDefault() {
		if($this->identity->id())
			$this->response->redirect('chyba/404');
	}

	public function renderDefault() {
		$this->template->title = 'Přihlášení';
		$this->template->description = 'Přihlášení k vlastnímu účtu';
	}

	public function submitEnterForm($post) {
		try {
			$this->csrf->defend();
			(new Constraint\FillRule('Přezdívka musí být vyplněna'))
			->isSatisfied($post->username);
			(new Constraint\FillRule('Heslo musí být vyplněno'))
			->isSatisfied($post->password);
			$identity = (new Access\TemporaryLogin(
				$this->storage(),
				new Security\AES256CBC(
					$this->configuration->toSection('cryptography')->key
				)
			))->enter(new User\User($post->username, $post->password));
			$this->checkBan($identity);
			$this->session[Access\Identity::ID] = $identity->id();
			$this->session[Access\Identity::ROLE] = (string)$identity->role();
			$this->session[Access\Identity::USERNAME] = $identity->username();
			$this->flashMessage->flash('Jsi úspěšně přihlášen', 'success');
			$this->response->redirect('');
		} catch(Exception\AccessDeniedException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
		} catch(Exception\FormatException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
		} catch(Exception\CsrfException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
		}
	}
}