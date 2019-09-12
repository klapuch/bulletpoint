<?php
declare(strict_types = 1);

namespace Bulletpoint\Domain\Upload;

abstract class Files {
	protected const PATH = __DIR__ . '/../../../data';

	abstract public function save(): int;
}
