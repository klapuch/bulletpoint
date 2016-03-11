<?php
namespace Bulletpoint\Model\Wiki;

use Bulletpoint\Model\Access;

interface Documents {
	public function iterate(Access\Identity $identity): \Iterator;
	public function add(
		string $title,
		string $description,
		InformationSource $source
	);
}