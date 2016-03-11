<?php
namespace Bulletpoint\Model\Wiki;

use Bulletpoint\Model\Access;

interface Bulletpoints {
	public function byIdentity(Access\Identity $identity): \Iterator;
	public function byDocument(Document $document): \Iterator;
	public function add(
		Document $document,
		string $content,
		InformationSource $source
	);
}