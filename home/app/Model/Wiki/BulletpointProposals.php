<?php
namespace Bulletpoint\Model\Wiki;

interface BulletpointProposals {
	public function iterate(): \Iterator;
	public function propose(
		Document $document,
		string $content,
		InformationSource $source
	);
}