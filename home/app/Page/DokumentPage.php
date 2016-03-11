<?php
namespace Bulletpoint\Page;

use Texy;
use Bulletpoint\Core;
use Bulletpoint\Model\Paths;
use Bulletpoint\Model\{
	User, Constraint, Wiki, Text, Translation, Conversation, Report, Rating
};
use Bulletpoint\Core\{Security, Filesystem, Storage};
use Bulletpoint\Exception;

final class DokumentPage extends BasePage {
	private function document(string $slug): Wiki\Document {
		try {
			(new Constraint\DocumentSlugExistenceRule($this->storage()))
			->isSatisfied($slug);
			$id = (new Translation\MySqlDocumentSlug(
				$slug,
				$this->storage()
			))->origin();
			return new Wiki\MySqlDocument($id, $this->storage());
		} catch(Exception\ExistenceException $ex) {
			$this->response->redirect('chyba/404');
		}
	}

	public function actionZobrazit(string $slug) {
		$this->template->slug = $slug;
		$this->template->document = $this->document($slug);
		$description = $this->template->document->description();
		$this->template->description = $description;
		$this->template->id = $this->template->document->id();
	}

	private function photo(Conversation\Comment $comment) {
		return (new User\ProfilePhoto(
			$comment->author(),
			new Filesystem\Folder(Paths::profileImage())
		))->show()->asFile()->location();
	}

	private function rating(Wiki\Bulletpoint $bulletpoint) {
		return new Rating\MySqlBulletpointRating(
			$bulletpoint,
			$this->identity,
			$this->storage()
		);
	}

	private function userRating(Wiki\Bulletpoint $bulletpoint) {
		return new Rating\MySqlUserBulletpointRating(
			$bulletpoint,
			$this->identity,
			$this->storage(),
			$this->rating($bulletpoint)
		);
	}

	public function renderZobrazit(string $slug) {
		$this->template->publishingFormat = new Text\PublishingFormat(new Texy);
		$this->template->title = $this->template->document->title();
		$this->template->comments = (new Conversation\MySqlDiscussion(
			$this->template->document->id(),
			$this->identity,
			$this->storage()
		))->contributions();
		$this->template->photo = function(Conversation\Comment $comment) {
			return $this->photo($comment);
		};
		$this->template->bulletpoints = (new Wiki\CategorizedMySqlBulletpoints(
			$this->identity,
			$this->storage(),
			$this->template->document
		))->iterate();
		$this->template->rating = function(Wiki\Bulletpoint $bulletpoint) {
			return $this->rating($bulletpoint);
		};
		$this->template->userRating = function(Wiki\Bulletpoint $bulletpoint) {
			return $this->userRating($bulletpoint);
		};
		$this->template->targets = iterator_to_array(
			(new Report\MySqlUserTargets(
				$this->identity,
				$this->storage()
			))->iterate()
		);
	}

	public function renderNovy() {
		$this->template->title = 'Nový dokument';
	}

	public function submitNewDocumentForm($post) {
		try {
			$this->csrf->defend();
			(new Constraint\ChainRule(
				new Constraint\FillRule('Titulek musí být vyplněn'),
				new Constraint\TitleRule	
			))->isSatisfied($post->documentTitle);
			(new Constraint\FillRule('Popis musí být vyplněn'))
			->isSatisfied($post->description);
			(new Constraint\YearRule)->isSatisfied($post->year);
			(new Wiki\MySqlDocumentProposals(
				$this->identity,
				$this->storage()
			))->propose(
				$post->documentTitle,
				$post->description,
				(new Wiki\MySqlInformationSources($this->storage()))
				->create($post->place, $post->year, $post->author)
			);
			$this->flashMessage->flash('Dokument byl zaslán ke kontrole', 'success');
			$this->response->redirect();
		} catch(Exception\FormatException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
		} catch(Exception\CsrfException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
		} catch(Exception\DuplicateException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
			$this->flashMessage->flash('Zkus upřesnit titulek', 'info');
		}
	}

	public function actionUpravit(string $slug) {
		$this->template->slug = $slug;
		$this->template->document = $this->document($slug);
	}

	public function renderUpravit(string $slug) {
		$this->template->slug = $slug;
		$this->template->title = 'Úprava dokumentu';
	}

	public function submitEditDocumentForm($post) {
		try {
			$this->csrf->defend();
			(new Constraint\ChainRule(
				new Constraint\FillRule('Titulek musí být vyplněn'),	
				new Constraint\TitleRule
			))->isSatisfied($post->documentTitle);
			$slug = (new Storage\Transaction($this->storage()))
			->start(function() use ($post) {
				$this->template->document->edit(
					$post->documentTitle,
					$post->description
				);
				return (new Translation\RestrictedSlug(
					new Translation\MySqlDocumentSlug(
						$this->template->slug,
						$this->storage()
				)))->rename((new Core\Text\WebalizedCorrection)
				->replacement($post->documentTitle));
			});
			$this->flashMessage->flash('Dokument byl upraven', 'success');
			$this->response->redirect('dokument/zobrazit/' . (string)$slug);
		} catch(Exception\FormatException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
		} catch(Exception\CsrfException $ex) {
			$this->flashMessage->flash($ex->getMessage(), 'danger');
		}
	}
}