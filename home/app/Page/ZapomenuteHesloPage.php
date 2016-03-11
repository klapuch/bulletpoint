<?php
namespace Bulletpoint\Page;

use Bulletpoint\Model\{Access, Constraint, Email};
use Bulletpoint\Core\{Security, Storage};
use Bulletpoint\Exception;

final class ZapomenuteHesloPage extends BasePage {
	public function renderDefault() {
		$this->template->title = 'Zapomenuté heslo';
		$this->template->description = 'Zapomenuté heslo k registrovanému účtu';
		$this->template->captcha = $captcha = new Security\ImageCaptcha();
		$this->session['captcha'] = serialize($captcha);
	}

	public function renderManualniZadani() {
		$this->template->title = 'Manuální zadání kódu pro obnovu hesla';
		$this->template->description = 'Manuální zadání kódu pro obnovu hesla k registrovanému účtu';
	}

	public function submitCodeVerificationForm($post) {
		try {
			$this->csrf->defend();
			$this->response->redirect('zapomenute-heslo/reset/' . $post->code);
		} catch(Exception\CsrfException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
		}
	}

	public function submitForgottenPasswordForm($post) {
		try {
			unserialize($this->session['captcha'])->verify($post->captcha);
			(new Constraint\ChainRule(
				new Constraint\FillRule('Email musí být vyplněn'),
				new Constraint\EmailRule,
				new Constraint\EmailExistenceRule($this->storage())
			))->isSatisfied($post->email);
			(new Storage\Transaction($this->storage()))
			->start(function() use($post) {
				(new Access\LimitedForgottenPasswords(
					new Access\MySqlForgottenPasswords(
						$this->storage()
					),
					$this->storage()
				))->remind($post->email);
				(new Email\MailService)->send(
					new Email\MailMessage(
						new Email\ForgottenPasswordMessage(
							$post->email,
							$this->storage()
						)
					)
				);
			});
			$this->flashMessage->flash(
				'Na email ti byly zaslány informace pro změnu hesla',
				'success'
			);
			$this->response->redirect();
		} catch(Exception\FormatException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
		} catch(Exception\CsrfException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
		} catch(Exception\StorageException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
		} catch(Exception\AccessDeniedException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
		} catch(Exception\ExistenceException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
		} catch(\OverflowException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
		}
	}

	public function actionReset(string $reminder) {
		try {
			(new Constraint\ChainRule(
				new Constraint\FillRule('Kód musí být vyplněn'),
				new Constraint\ReminderRule($this->storage())
			))->isSatisfied($reminder);
			$this->template->reminder = $reminder;
		} catch(Exception\FormatException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
			$this->response->redirect('zapomenute-heslo/manualni-zadani');
		} catch(Exception\ExistenceException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
			$this->response->redirect('zapomenute-heslo/manualni-zadani');
		} catch(Exception\DuplicateException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
			$this->response->redirect('zapomenute-heslo/manualni-zadani');
		}
	}

	public function renderReset(string $reminder) {
		$this->template->title = 'Změna zapomenutého hesla';
	}

	public function submitPasswordChangeForm($post) {
		try {
			$this->csrf->defend();
			(new Constraint\ChainRule(
				new Constraint\FillRule('Nové heslo musí být vyplněno'),
				new Constraint\SameRule('Hesla se neshodují', $post->repeatedPassword),
				new Constraint\PasswordRule
			))->isSatisfied($post->password);
			(new Storage\Transaction($this->storage()))
			->start(function() use($post) {
				(new Access\MySqlForgottenPassword(
					$this->template->reminder,
					$this->storage(),
					new Security\AES256CBC(
						$this->configuration->toSection('cryptography')->key
					)
				))->change($post->password);
			});
			$this->flashMessage->flash('Heslo bylo změněno', 'success');
			$this->response->redirect('prihlasit');
		} catch(Exception\FormatException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
		} catch(Exception\CsrfException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
		} catch(Exception\StorageException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
		}
	}
}