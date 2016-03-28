<?php
namespace Bulletpoint\Model\Storage;

use Bulletpoint\Exception;

final class Transaction {
    private $database;

    public function __construct(Database $database) {
        $this->database = $database;
    }

    public function start(\Closure $callback) {
        try {
            $this->database->exec('START TRANSACTION');
            $result = $callback();
            $this->database->exec('COMMIT');
            return $result;
        } catch(\Throwable $ex) {
            $this->database->exec('ROLLBACK');
            if($ex instanceof \PDOException) {
                throw new Exception\StorageException(
                    'Nastala chyba na straně úložiště.',
                    null,
                    $ex
                );
            }
            throw $ex;
        }
    }
}