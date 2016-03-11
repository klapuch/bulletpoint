<?php
namespace Bulletpoint\Core\Access;

interface Acl {
	public function list(): array;
}