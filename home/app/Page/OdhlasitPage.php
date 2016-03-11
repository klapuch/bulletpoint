<?php
namespace Bulletpoint\Page;

use Bulletpoint\Exception;
use Bulletpoint\Model\{Access};

final class OdhlasitPage extends BasePage {
	public function actionDefault() {
		try {
			$this->csrf->defend();
			if(!$this->identity->id())
				$this->response->redirect('prihlasit');
			unset($this->session[Access\Identity::ID]);
			unset($this->session[Access\Identity::ROLE]);
			unset($this->session[Access\Identity::USERNAME]);
			$this->flashMessage->flash('Jsi úspěšně odhlášen', 'success');
			session_regenerate_id(true);
		} catch(Exception\CsrfException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
		} finally {
			$this->response->redirect('');
		}
	}
}