<?php
declare(strict_types = 1);

namespace Bulletpoint\Scheduling;

require __DIR__ . '/../../vendor/autoload.php';

use Bulletpoint\Configuration;
use Klapuch\Configuration\ValidIni;
use Klapuch\Scheduling;
use Klapuch\Storage;
use Predis;
use Sentry;
use Tracy;

Sentry\init([
	'dsn' => 'https://7fdbd2012f66406dbba931907ca95a9d@sentry.io/1447942',
	'environment' => getenv('BULLETPOINT_ENV') ?: 'local',
	'tags' => ['index' => 'cron'],
]);

$configuration = (new Configuration\ApplicationConfiguration())->read();

$connection = new Storage\CachedConnection(
	new Storage\PDOConnection(
		new Storage\SafePDO(
			$configuration['DATABASE']['dsn'],
			$configuration['DATABASE']['user'],
			$configuration['DATABASE']['password'],
		),
	),
	new Predis\Client($configuration['REDIS']['uri']),
);

$logger = new Tracy\Logger(__DIR__ . '/../../logs');

try {
	(new Scheduling\SelectedJob(
		$argv[1],
		new Scheduling\MarkedJob(new Task\Cron($connection), $connection),
		new Task\CheckChangedConfiguration(
			new \SplFileInfo(__DIR__ . '/../../../docker/nginx'),
			new Scheduling\SerialJobs(
				new Task\GenerateNginxRoutes(
					new ValidIni(new \SplFileInfo(__DIR__ . '/../Configuration/routes.ini')),
					new \SplFileInfo(__DIR__ . '/../../../docker/nginx/routes.conf'),
				),
				new Task\GenerateNginxConfiguration(
					new \SplFileInfo(__DIR__ . '/../../../docker/nginx/preflight.conf'),
				),
			),
		),
		new Scheduling\MarkedJob(
			new Task\GenerateNginxRoutes(
				new ValidIni(new \SplFileInfo(__DIR__ . '/../Configuration/routes.ini')),
				new \SplFileInfo(__DIR__ . '/../../../docker/nginx/routes.conf'),
			),
			$connection,
		),
		new Scheduling\MarkedJob(new Task\GenerateJsonSchema($connection), $connection),
		new Scheduling\MarkedJob(
			new Task\GenerateNginxConfiguration(
				new \SplFileInfo(__DIR__ . '/../../../docker/nginx/preflight.conf'),
			),
			$connection,
		),
		new Task\PlPgSqlCheck(
			$connection,
			new ValidIni(new \SplFileInfo(__DIR__ . '/Task/plpgsql_check.ini')),
		),
	))->fulfill();
} catch(\Throwable $e) {
	$logger->log($e);
	\Sentry\captureException($e);
	throw $e;
}
