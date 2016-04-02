<?php
namespace Bulletpoint\Model\Storage;

use Tracy;

final class ProfiledDatabase implements Database {
    private $origin;

    public function __construct(Database $origin) {
        $this->origin = $origin;
    }

    public function fetch(string $query, array $parameters = []) {
        $this->dump($query);
        return $this->origin->fetch($query, $parameters);
    }

    public function fetchAll(string $query, array $parameters = []) {
        $this->dump($query);
        return $this->origin->fetchAll($query, $parameters);
    }

    public function fetchColumn(string $query, array $parameters = []) {
        $this->dump($query);
        return $this->origin->fetchColumn($query, $parameters);
    }

    public function query(string $query, array $parameters = []) {
        $this->dump($query);
        return $this->origin->query($query, $parameters);
    }

    public function exec(string $query) {
        $this->dump($query);
        $this->origin->exec($query);
    }

    private function dump($query) {
        Tracy\Debugger::barDump($query, 'Query');
    }
}