<?php
declare(strict_types = 1);

require __DIR__ . '/../vendor/autoload.php';

use Bulletpoint\Configuration\ApplicationConfiguration;
use Bulletpoint\Routing\ApplicationRoutes;
use Klapuch\Application;
use Klapuch\Encryption;
use Klapuch\Output;
use Klapuch\Routing;
use Klapuch\Storage;
use Klapuch\Uri;

Sentry\init([
	'dsn' => 'https://7fdbd2012f66406dbba931907ca95a9d@sentry.io/1447942',
	'environment' => $_SERVER['BULLETPOINT_ENV'],
	'tags' => ['index' => 'www'],
]);

$uri = new Uri\CachedUri(
	new Uri\BaseUrl(
		$_SERVER['SCRIPT_NAME'],
		$_SERVER['REQUEST_URI'],
		$_SERVER['SERVER_NAME'],
		isset($_SERVER['HTTPS']) ? 'https' : 'http',
	),
);

$configuration = (new ApplicationConfiguration())->read();

echo (new class(
	$configuration,
	new Routing\MatchingRoutes(
		new Routing\NginxMatchedRoutes(
			new ApplicationRoutes(
				new Storage\CachedConnection(
					new Storage\PDOConnection(
						new Storage\SafePDO(
							$configuration['DATABASE']['dsn'],
							$configuration['DATABASE']['user'],
							$configuration['DATABASE']['password'],
						),
					),
					new SplFileInfo(__DIR__ . '/../temp'),
				),
				$uri,
				new Encryption\PasswordHash(),
			),
		),
		$uri,
		$_SERVER['REQUEST_METHOD'],
	),
	new Tracy\Logger(__DIR__ . '/../logs')
) implements Output\Template {
	/** @var mixed[] */
	private array $configuration;

	private \Klapuch\Routing\Routes $routes;

	private \Tracy\ILogger $logger;

	public function __construct(array $configuration, Routing\Routes $routes, Tracy\ILogger $logger) {
		$this->configuration = $configuration;
		$this->routes = $routes;
		$this->logger = $logger;
	}

	public function render(array $variables = []): string {
		try {
			$match = $this->routes->matches();
			$destination = current($match);
			assert($destination instanceof Closure);
			return (new Application\RawTemplate(
				$destination()->response(
					(new Routing\TypedMask(
						new Routing\CombinedMask(
							new Routing\NginxMask(),
							new Routing\CommonMask(),
						),
					))->parameters(),
				),
			))->render();
		} catch (\Throwable $e) {
			$this->logger->log($e);
			\Sentry\captureException($e);
			if ($e instanceof \UnexpectedValueException) {
				return (new Application\RawTemplate(
					new Bulletpoint\Response\JsonError($e),
				))->render();
			}
			return (new Application\RawTemplate(
				new Bulletpoint\Response\JsonError(
					new \UnexpectedValueException(),
					[],
					HTTP_INTERNAL_SERVER_ERROR,
				),
			))->render();
		}
	}
})->render();
