<?php
namespace Bulletpoint\Page;

use Texy;
use Bulletpoint\Model\Paths;
use Bulletpoint\Model\{User, Constraint, Wiki, Text, Translation};
use Bulletpoint\Core;
use Bulletpoint\Core\{Security, Filesystem, Storage};
use Bulletpoint\Exception;

final class NavrhPage extends AdminBasePage {
	private function bulletpoint(int $id): Wiki\BulletpointProposal {
		try {
			(new Constraint\BulletpointProposalExistenceRule($this->storage()))
			->isSatisfied($id);
			return new Wiki\MySqlBulletpointProposal(
				$id,
				$this->identity,
				$this->storage()
			);
		} catch(Exception\ExistenceException $ex) {
			$this->response->redirect('chyba/404');
		}
	}

	private function document(int $id): Wiki\DocumentProposal {
		try {
			(new Constraint\DocumentProposalExistenceRule($this->storage()))
			->isSatisfied($id);
			return new Wiki\MySqlDocumentProposal(
				$id,
				$this->identity,
				$this->storage()
			);
		} catch(Exception\ExistenceException $ex) {
			$this->response->redirect('chyba/404');
		}
	}

	public function renderBulletpointDetail(int $id) {
		$this->template->id = $id;
		$proposal = $this->bulletpoint($id);
		$this->template->publishingFormat = new Text\PublishingFormat(new Texy);
		$this->template->proposal = $proposal;
		$document = $proposal->document();
		$this->template->document = $document;
		$this->template->title = $document->title();
		$this->template->bulletpoints = (new Wiki\MySqlBulletpoints(
			$this->identity,
			$this->storage()
		))->byDocument($document);
		$this->template->photo = (new User\ProfilePhoto(
			$proposal->author(),
			new Filesystem\Folder(Paths::profileImage())
		))->show()->asFile()->location();
	}

	public function renderDokumentDetail(int $id) {
		$this->template->id = $id;
		$proposal = $this->document($id);
		$this->template->publishingFormat = new Text\PublishingFormat(new Texy);
		$this->template->proposal = $proposal;
		$this->template->title = $proposal->title();
	}

	public function renderPrijmoutBulletpoint(int $id) {}
	public function renderOdmitnoutBulletpoint(int $id, string $reason = null) {}

	public function actionPrijmoutBulletpoint(int $id) {
		try {
			$this->csrf->defend();
			$bulletpoint = $this->bulletpoint($id);
			(new Storage\Transaction($this->storage()))
			->start(function() use($bulletpoint) {
				$bulletpoint->accept();
			});
			$this->flashMessage->flash('Návrh byl přijat', 'success');
		} catch(Exception\CsrfException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
			$this->response->redirect('navrh/bulletpoint-detail/' . $id);
		} catch(Exception\DuplicateException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
			$this->response->redirect('navrh/bulletpoint-detail/' . $id);
		} catch(Exception\StorageException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
			$this->response->redirect('navrh/bulletpoint-detail/' . $id);
		} finally {
			$this->response->redirect('navrhy/bulletpointy');
		}
	}

	public function actionOdmitnoutBulletpoint(int $id, string $reason = null) {
		try {
			$this->csrf->defend();
			$this->bulletpoint($id)->reject($reason);
			$this->flashMessage->flash('Návrh byl odmítnut', 'success');
		} catch(Exception\CsrfException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
		} catch(Exception\StorageException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
		} finally {
			$this->response->redirect('navrhy/bulletpointy');
		}
	}

	public function renderPrijmoutDokument(int $id) {}
	public function renderOdmitnoutDokument(int $id, string $reason = null) {}

	public function actionPrijmoutDokument(int $id) {
		try {
			$this->csrf->defend();
			$document = $this->document($id);
			(new Storage\Transaction($this->storage()))
			->start(function() use($document) {
				$acceptedDocument = $document->accept();
				(new Translation\MySqlDocumentSlugs(
					$this->storage(),
					new Core\Text\WebalizedCorrection
				))->add($acceptedDocument->id(), $acceptedDocument->title());
			});
			$this->flashMessage->flash('Návrh byl přijat', 'success');
		} catch(Exception\CsrfException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
			$this->response->redirect('navrh/dokument-detail/' . $id);
		} catch(Exception\DuplicateException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
			$this->response->redirect('navrh/dokument-detail/' . $id);
		} catch(Exception\StorageException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
			$this->response->redirect('navrh/dokument-detail/' . $id);
		} finally {
			$this->response->redirect('navrhy/dokumenty');
		}
	}

	public function actionOdmitnoutDokument(int $id, string $reason = null) {
		try {
			$this->csrf->defend();
			$this->document($id)->reject($reason);
			$this->flashMessage->flash('Návrh byl odmítnut', 'success');
		} catch(Exception\CsrfException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
		} catch(Exception\StorageException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
		} finally {
			$this->response->redirect('navrhy/dokumenty');
		}
	}

	public function actionUpravitDokument(int $id) {
		$this->template->id = $id;
		$this->template->proposal = $this->document($id);
	}

	public function renderUpravitDokument(int $id) {
		$this->template->title = 'Úprava dokumentu';
	}

	public function submitEditDocumentForm($post) {
		try {
			$this->csrf->defend();
			(new Constraint\ChainRule(
				new Constraint\FillRule('Titulek musí být vyplněn'),	
				new Constraint\TitleRule
			))->isSatisfied($post->documentTitle);
			(new Constraint\FillRule('Popis musí být vyplněn'))
			->isSatisfied($post->description);
			$this->template->proposal->edit(
				$post->documentTitle,
				$post->description
			);
			$this->flashMessage->flash('Návrh byl upraven', 'success');
			$this->response->redirect(
				'navrh/dokument-detail/' . $this->template->id
			);
		} catch(Exception\FormatException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
		} catch(Exception\CsrfException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
		} catch(Exception\DuplicateException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
		}
	}

	public function actionUpravitBulletpoint(int $id) {
		$this->template->id = $id;
		$this->template->proposal = $this->bulletpoint($id);
	}

	public function renderUpravitBulletpoint(int $id) {
		$this->template->title = 'Úpráva bulletpointu k dokumentu';
	}

	public function submitEditBulletpointForm($post) {
		try {
			$this->csrf->defend();
			(new Constraint\FillRule('Obsah musí být vyplněn'))
			->isSatisfied($post->content);
			$this->template->proposal->edit($post->content);
			$this->flashMessage->flash('Návrh byl upraven', 'success');
			$this->response->redirect(
				'navrh/bulletpoint-detail/' . $this->template->id
			);
		} catch(Exception\CsrfException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
		} catch(Exception\FormatException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');	
		} catch(Exception\DuplicateException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
		}
	}
}