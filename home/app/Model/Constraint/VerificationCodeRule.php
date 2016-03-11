<?php
namespace Bulletpoint\Model\Constraint;

use Bulletpoint\Core\Storage;
use Bulletpoint\Exception;

final class VerificationCodeRule implements Rule {
	private $database;

	public function __construct(Storage\Database $database) {
		$this->database = $database;
	}

	public function isSatisfied($input) {
		if(!(bool)preg_match('~^[a-f0-9]{50}:[a-f0-9]{40}\z~i', $input)) {
			throw new Exception\FormatException(
				'Ověřovací kód nemá správný formát'
			);
		} elseif(!$this->exists($input)) {
			throw new Exception\ExistenceException(
				'Ověřovací kód neexistuje'
			);
		}
	}

	private function exists(string $code): bool {
		return (bool)$this->database->fetch(
			'SELECT 1 FROM verification_codes WHERE code = ?',
			[$code]
		);
	}
}