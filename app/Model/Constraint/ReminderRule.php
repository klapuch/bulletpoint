<?php
namespace Bulletpoint\Model\Constraint;

use Bulletpoint\Exception;
use Bulletpoint\Model\Storage;

final class ReminderRule implements Rule {
    const EXPIRATION = 24; // hours
    private $database;

    public function __construct(Storage\Database $database) {
        $this->database = $database;
    }

    public function isSatisfied($input) {
        if(!(bool)preg_match('~^[a-f0-9]{100}:[a-f0-9]{40}\z~i', $input)) {
            throw new Exception\FormatException(
                'Obnovovací kód nemá správný formát'
            );
        } elseif(!$this->exists($input)) {
            throw new Exception\ExistenceException(
                'Obnovovací kód neexistuje'
            );
        } elseif($this->used($input)) {
            throw new Exception\DuplicateException(
                'Obnovovací kód byl již využit'
            );
        } elseif($this->expired($input)) {
            throw new Exception\ExistenceException(
                sprintf(
                    'Obnovovací kód pozbyl platnosti %d hodin',
                    self::EXPIRATION
                )
            );
        }
    }

    private function used(string $reminder): bool {
        return (bool)$this->database->fetch(
            'SELECT 1
			FROM forgotten_passwords
			WHERE reminder = ?
			AND used = 1',
            [$reminder]
        );
    }

    private function expired(string $reminder): bool {
        return (bool)$this->database->fetch(
            'SELECT 1
			FROM forgotten_passwords
			WHERE reminder = ?
			AND TIMESTAMPDIFF(HOUR, reminded_at, NOW()) > ?',
            [$reminder, self::EXPIRATION]
        );
    }

    private function exists(string $reminder): bool {
        return (bool)$this->database->fetch(
            'SELECT 1
			FROM forgotten_passwords
			WHERE reminder = ?',
            [$reminder]
        );
    }
}