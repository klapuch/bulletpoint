<?php
declare(strict_types = 1);

namespace Bulletpoint\Fixtures;

use Klapuch\Storage;

final class SamplePostgresData implements Sample {
	private Storage\Connection $connection;

	private string $sample;

	/** @var mixed[] */
	private array $data;

	public function __construct(Storage\Connection $connection, string $sample, array $data = []) {
		$this->connection = $connection;
		$this->sample = $sample;
		$this->data = $data;
	}

	public function try(): array {
		return (new Storage\NativeQuery(
			$this->connection,
			sprintf('SELECT samples.%s(?) AS id', $this->sample),
			[json_encode($this->data, JSON_FORCE_OBJECT)],
		))->row();
	}
}
