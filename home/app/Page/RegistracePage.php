<?php
namespace Bulletpoint\Page;

use Bulletpoint\Model\{Access, Constraint, Email, User};
use Bulletpoint\Core\{Security, Storage};
use Bulletpoint\Exception;

final class RegistracePage extends BasePage {
	public function actionDefault() {
		if($this->identity->id())
			$this->response->redirect('chyba/404');
	}

	public function renderDefault() {
		$this->template->title = 'Registrace';
		$this->template->description = 'Registrace vlastního účtu';
		$this->template->captcha = $captcha = new Security\ImageCaptcha();
		$this->session['captcha'] = serialize($captcha);
	}

	public function submitRegisterForm($post) {
		try {
			(new Constraint\ChainRule(
				new Constraint\FillRule('Přezdívka musí být vyplněna'),
				new Constraint\UsernameRule,
				new Constraint\UsernameNotExistenceRule($this->storage())
			))->isSatisfied($post->username);
			(new Constraint\ChainRule(
				new Constraint\FillRule('Email musí být vyplněn'),
				new Constraint\EmailRule,
				new Constraint\EmailNotExistenceRule($this->storage())
			))->isSatisfied($post->email);
			(new Constraint\ChainRule(
				new Constraint\FillRule('Heslo musí být vyplněno'),
				new Constraint\SameRule('Hesla se neshodují', $post->repeatedPassword),
				new Constraint\PasswordRule
			))->isSatisfied($post->password);
			unserialize($this->session['captcha'])->verify($post->captcha);
			(new Storage\Transaction($this->storage()))
			->start(function() use($post) {
				(new Access\Registration(
					$this->storage(),
					new Security\AES256CBC(
						$this->configuration->toSection('cryptography')->key
					)
				))->register(
					new User\Applicant(
						new User\User($post->username, $post->password),
						$post->email
					)
				);
				(new Access\MySqlVerificationCodes($this->storage()))
				->generate($post->email);
				(new Email\MailService)->send(
					new Email\MailMessage(
						new Email\ActivationMessage(
							$post->email,
							$this->storage()
						)
					)
				);
			});
			$this->flashMessage->flash('Jsi úspěšně registrován', 'success');
			$this->flashMessage->flash('Na uvedený email ti byl zaslán aktivační kód', 'warning');
			$this->response->redirect('aktivace/manualni-zadani');
		} catch(Exception\FormatException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
		} catch(Exception\CsrfException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
		} catch(Exception\StorageException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
		} catch(Exception\ExistenceException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
		} catch(Exception\AccessDeniedException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
		}
	}
}