<?php
namespace Bulletpoint\Core\Access;

use Bulletpoint\Core\Http;

interface Authorization {
	public function hasAccess(Http\Address $address): bool;
}