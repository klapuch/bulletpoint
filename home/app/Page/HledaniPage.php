<?php
namespace Bulletpoint\Page;

use Bulletpoint\Model\{Access, Constraint, Wiki};
use Bulletpoint\Core\{Security, Filesystem};
use Bulletpoint\Exception;

final class HledaniPage extends BasePage {
	public function renderDefault() {
		$keyword = trim($_GET['keyword']);
		$matches = (new Wiki\Fulltext(
			$this->storage()
		))->matches($keyword);
		$this->template->count = key($matches);
		$this->template->matches = current($matches);
		if(key($matches) === 1) {
			$this->response->redirect(
				'dokument/zobrazit/' . $matches[1][0]['slug']
			);
		}
		$this->template->title = 'Výsledky pro ' . $keyword . '...';
		$this->template->description = 'Výsledky hledání pro klíčové slovo "' . $keyword . '"' ;
	}
}