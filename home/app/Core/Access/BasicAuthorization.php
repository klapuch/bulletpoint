<?php
namespace Bulletpoint\Core\Access;

use Bulletpoint\Core\Http;
use Bulletpoint\Exception;

final class BasicAuthorization implements Authorization {
	private $acl;
	private $comparison;

	public function __construct(Acl $acl, Comparison $comparison) {
		$this->acl = $acl;
		$this->comparison = $comparison;
	}

	public function hasAccess(Http\Address $address): bool {
		return count($this->forbiddenAddresses($address)) === 0;
	}

	/**
	* When address(current) is same as any other address in Acl, then access is denied
	*/
	private function forbiddenAddresses(Http\Address $address): array {
		$pathname = implode('/', $address->pathname());
		return array_filter(
			$this->acl->list(),
			function($allowedAddress) use ($pathname) {
				return strlen($pathname)
				&& $this->comparison->areSame($allowedAddress, $pathname);
			}
		);
	}
}