<?php
namespace Bulletpoint\Core\Control;

interface Log {
	public function write(array $errors);
	public function read(): string;
}