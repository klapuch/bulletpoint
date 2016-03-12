<?php
namespace Bulletpoint\Page;

use Bulletpoint\Model\{Access, Constraint};
use Bulletpoint\Exception;
use Bulletpoint\Core;
use Bulletpoint\Core\{Http, Security, UI, Storage};

abstract class BasePage extends Page {
	protected $response;
	protected $identity;
	protected $flashMessage;
	protected $session;
	protected $csrf;
	private $database;
	const ACCESS_DENIED = 403;

	protected function setUp() {
		$this->session = new Http\Session($_SESSION);
		$this->identity = new Access\ConstantIdentity(
			(int)$this->session[Access\Identity::ID],
			new Access\ConstantRole(
				(string)$this->session[Access\Identity::ROLE],
				new Access\MySqlRole(
					(int)$this->session[Access\Identity::ID],
					$this->storage()
				)
			),
			(string)$this->session[Access\Identity::USERNAME]
		);
		if(!$this->hasAccess()) {
			throw new Exception\AccessDeniedException(
				'Na tuto adresu nemáte přístup',
				self::ACCESS_DENIED
			);
		}
		$this->flashMessage = new UI\FlashMessage($_SESSION);
		$this->response = new Http\Response($this->request->address());
		$this->checkPunishments($this->identity);
		$this->csrf = $this->csrf();
		$this->template->request = $this->request;
		$this->template->response = $this->response;
		$this->template->csrf = $this->csrf;
		$this->template->identity = $this->identity;
		$this->template->flashMessage = $this->flashMessage;
		$this->template->addFilter(
			'hasAccess', function($url) {
				return $this->hasAccess($url);
			}
		);
	}

	private function csrf(): Security\CsrfProtection {
		$csrf = new Security\CsrfProtection(
			$this->session,
			$this->request
		);
		$this->session[$csrf->key()] = $csrf->protection();
		return $csrf;
	}

	protected function storage(): Storage\Database {
		if($this->database)
			return $this->database;
		$this->database = new Storage\PDODatabase(
			...array_values(
				$this->configuration->toSection('database')->setting()
			)
		);
		return $this->database;
	}

	protected function hasAccess(string $url = null): bool {
		if($url === null)
			$address = $this->request->address();
		else
			$address = new Http\ExplicitUrl($url);
		return (new Core\Access\BasicAuthorization(
			new Core\Access\RoleBasedAcl(
				$this->configuration,
				(string)$this->identity->role()
			),
			new Core\Access\WildcardComparison
		))->hasAccess($address);
	}

	protected function checkPunishments(Access\Identity $identity) {
		$punishments = (new Constraint\OwnedMySqlPunishments(
			$identity,
			$this->storage(),
			new Constraint\ActualMySqlPunishments(
				$identity,
				$this->storage()
			)
		))->iterate();
		if($punishments->valid()) {
			$punishment = $punishments->current();
			if($punishment->expired() === false) {
				unset($this->session[Access\Identity::ID]);
				unset($this->session[Access\Identity::ROLE]);
				unset($this->session[Access\Identity::USERNAME]);
				$this->flashMessage->flash(
					sprintf(
						'Tvůj účet je zablokován do %s z důvodu %s',
						$punishment->expiration()->format('j.n.Y H:i'),
						$punishment->reason()
					), 'danger'
				);
				$this->response->redirect('prihlasit');
			}
		}
	}
}