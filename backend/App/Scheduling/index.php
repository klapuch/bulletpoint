<?php
declare(strict_types = 1);

namespace Bulletpoint\Scheduling;

require __DIR__ . '/../../vendor/autoload.php';

use Bulletpoint;
use Bulletpoint\Configuration;
use Klapuch\Configuration\ValidIni;
use Klapuch\Storage;
use Predis;

$configuration = (new Configuration\ApplicationConfiguration())->read();

$connection = new Storage\CachedConnection(
	new Storage\PDOConnection(
		new Storage\SafePDO(
			$configuration['DATABASE']['dsn'],
			$configuration['DATABASE']['user'],
			$configuration['DATABASE']['password']
		)
	),
	new Predis\Client($configuration['REDIS']['uri'])
);

(new SelectedJob(
	$argv[1],
	new Bulletpoint\Scheduling\Task\CheckChangedConfiguration(
		new \SplFileInfo(__DIR__ . '/../../../docker/nginx'),
		new SerialJobs(
			new Task\GenerateNginxRoutes(
				new ValidIni(new \SplFileInfo(__DIR__ . '/../Configuration/.routes.ini')),
				new \SplFileInfo(__DIR__ . '/../../../docker/nginx/routes.conf')
			),
			new Bulletpoint\Scheduling\Task\GenerateNginxConfiguration(
				new \SplFileInfo(__DIR__ . '/../../../docker/nginx/preflight.conf')
			)
		)
	),
	new MarkedJob(
		new Task\GenerateNginxRoutes(
			new ValidIni(new \SplFileInfo(__DIR__ . '/../Configuration/.routes.ini')),
			new \SplFileInfo(__DIR__ . '/../../../docker/nginx/routes.conf')
		),
		$connection
	),
	new MarkedJob(new Task\GenerateJsonSchema($connection), $connection),
	new MarkedJob(
		new Bulletpoint\Scheduling\Task\GenerateNginxConfiguration(
			new \SplFileInfo(__DIR__ . '/../../../docker/nginx/preflight.conf')
		),
		$connection
	)
))->fulfill();
