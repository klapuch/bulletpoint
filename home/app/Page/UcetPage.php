<?php
namespace Bulletpoint\Page;

use Bulletpoint\Model\Paths;
use Bulletpoint\Model\{User, Constraint};
use Bulletpoint\Core\{Security, Filesystem};
use Bulletpoint\Exception;

final class UcetPage extends BasePage {
	public function actionDefault() {
		$this->template->account = new User\Account(
			$this->identity,
			$this->storage(),
			new Security\AES256CBC(
				$this->configuration->toSection('cryptography')->key
			)
		);
	}

	public function renderDefault() {
		$this->template->title = $this->identity->username();
		$this->template->photo = (new User\ProfilePhoto(
			$this->identity,
			new Filesystem\Folder(Paths::profileImage())
		))->show()->asFile()->location();
	}

	public function submitPasswordChangeForm($post) {
		try {
			$this->csrf->defend();
			(new Constraint\ChainRule(
				new Constraint\FillRule('Heslo musí být vyplněno'),
				new Constraint\SameRule('Hesla se neshodují', $post->repeatedPassword),
				new Constraint\PasswordRule
			))->isSatisfied($post->password);
			$this->template->account->changePassword($post->password);
			$this->flashMessage->flash('Heslo je změněno', 'success');
			$this->response->redirect();
		} catch(Exception\FormatException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
		} catch(Exception\CsrfException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
		}
	}

	public function submitPhotoChangeForm($post) {
		try {
			$this->csrf->defend();
			(new User\ProfilePhoto(
				$this->identity,
				new Filesystem\Folder(Paths::profileImage())))->change(
					new Filesystem\Image(
						new Filesystem\UploadedFile($_FILES['photo'])
					)
				);
			$this->flashMessage->flash('Fotka je změněna', 'success');
			$this->flashMessage->flash('Obnov stránku pro změnu', 'warning');
			$this->response->redirect();
		} catch(Exception\FormatException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
		} catch(Exception\CsrfException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
		} catch(Exception\UploadException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
		} catch(Exception\StorageException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
		}
	}
}