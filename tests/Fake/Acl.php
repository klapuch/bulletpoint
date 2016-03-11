<?php
namespace Bulletpoint\Fake;

use Bulletpoint\Core\Access;

final class Acl implements Access\Acl {
	private $list;

	public function __construct(array $list) {
		$this->list = $list;
	}

	public function list(): array {
		return $this->list;
	}
}