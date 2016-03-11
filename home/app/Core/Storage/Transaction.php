<?php
namespace Bulletpoint\Core\Storage;

use Bulletpoint\Exception;

final class Transaction {
	private $database;

	public function __construct(Database $database) {
		$this->database = $database;
	}

	public function start(Callable $callback) {
		try {
			$this->database->exec('START TRANSACTION');
			$result = $callback();
			$this->database->exec('COMMIT');
			return $result;
		} catch(\PDOException $ex) {
			$this->database->exec('ROLLBACK');
			throw new Exception\StorageException(
				'Nastala chyba na straně úložiště.',
				null,
				$ex
			);
		} catch(\Throwable $ex) {
			$this->database->exec('ROLLBACK');
			throw $ex;
		}
	}
}