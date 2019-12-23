<?php
declare(strict_types = 1);

namespace Bulletpoint\TestCase;

use Bulletpoint;
use Klapuch\Configuration;

trait Page {
	use TemplateDatabase {
		TemplateDatabase::setUp as databaseSetUp;
	}

	/** @var mixed[] */
	private array $configuration;

	protected function setUp(): void {
		parent::setUp();
		$this->configuration = (new Configuration\CombinedSource(
			new Bulletpoint\Configuration\ApplicationConfiguration(),
			new Configuration\ValidIni(
				new \SplFileInfo(__DIR__ . '/../Configuration/.secrets.ini'),
			),
		))->read();
		$this->databaseSetUp();
	}
}
