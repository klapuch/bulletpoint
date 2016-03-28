<?php
namespace Bulletpoint\Page;

use Bulletpoint\Model\Constraint;
use Bulletpoint\Component;
use Nette\Caching\Storages;

final class TrestyPage extends BasePage {
    public function createComponentPunishments() {
        return new Component\Punishments(
            new Constraint\CachedPunishments(
                new Constraint\ActualMySqlPunishments(
                    $this->identity,
                    $this->database
                ),
                new Storages\MemoryStorage
            ),
            $this->identity,
            $this->database
        );
    }
}