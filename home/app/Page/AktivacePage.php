<?php
namespace Bulletpoint\Page;

use Bulletpoint\Exception;
use Bulletpoint\Model\{Access, Constraint, Email};

final class AktivacePage extends BasePage {
	public function actionZnovuzaslani() {}
	public function renderZnovuzaslani() {
		$this->template->title = 'Znovuzaslání ověřovacího kódu';
		$this->template->description = 'Znovuzaslání ověřovacího kódu k registrovanému účtu';
	}

	public function submitSendAgainForm($post) {
		try {
			(new Constraint\ChainRule(
				new Constraint\FillRule('Email musí být vyplněn'),
				new Constraint\EmailRule,
				new Constraint\EmailExistenceRule($this->storage())
			))->isSatisfied($post->email);
			(new Access\ReserveVerificationCodes(
				$this->storage()
			))->generate($post->email);
			(new Email\MailService)->send(
				new Email\MailMessage(
					new Email\ActivationMessage(
						$post->email,
						$this->storage()
					)
				)
			);
			$this->flashMessage->flash('Ověřovací kód byl zaslán', 'success');
			$this->response->redirect('aktivace/manualni-zadani');
		} catch(Exception\FormatException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
		} catch(Exception\ExistenceException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
		}
	}

	public function renderManualniZadani() {
		$this->template->title = 'Manuální zadání ověřovacího kódu';
		$this->template->description = 'Manuální zadání ověřovacího kódu pro aktivaci vlastního účtu';
	}

	public function submitVerificateCodeForm($post) {
		try {
			$this->csrf->defend();
			$this->response->redirect('aktivace/aktivovat/' . $post->code);			
		} catch(Exception\CsrfException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
		}
	}

	public function renderAktivovat(string $code) {}
	public function actionAktivovat(string $code) {
		try {
			(new Constraint\ChainRule(
				new Constraint\FillRule('Ověřovací kód musí být vyplněn'),
				new Constraint\VerificationCodeRule($this->storage())
			))->isSatisfied($code);
			$verificationCode = new Access\MySqlVerificationCode(
				$code,
				$this->storage()
			);
			$verificationCode->use();
			$owner = $verificationCode->owner();
			$this->session[Access\Identity::ID] = $owner->id();
			$this->session[Access\Identity::ROLE] = (string)$owner->role();
			$this->session[Access\Identity::USERNAME] = $owner->username();
			$this->flashMessage->flash('Účet je aktivován', 'success');
			$this->flashMessage->flash('Jsi úspěšně přihlášen', 'success');
			$this->response->redirect('');
		} catch(Exception\FormatException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
		} catch(Exception\ExistenceException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger'); 
		} catch(Exception\DuplicateException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
		} finally {
			$this->response->redirect('aktivace/manualni-zadani');
		}
	}
}