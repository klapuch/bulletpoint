<?php
namespace Bulletpoint\Core\Storage;

interface Database {
	public function fetch(string $query, array $parameters, int $style);
	public function fetchAll(string $query, array $parameters, int $style);
	public function fetchColumn(string $query, array $parameters);
	public function query(string $query, array $parameters);
}