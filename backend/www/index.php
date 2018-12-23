<?php
declare(strict_types = 1);

require __DIR__ . '/../vendor/autoload.php';

use Bulletpoint\Configuration;
use Klapuch\Application;
use Klapuch\Output;
use Klapuch\Routing;
use Klapuch\Storage;
use Klapuch\Uri;
use Klapuch\Encryption;

$uri = new Uri\CachedUri(
	new Uri\BaseUrl(
		$_SERVER['SCRIPT_NAME'],
		$_SERVER['REQUEST_URI'],
		$_SERVER['SERVER_NAME'],
		isset($_SERVER['HTTPS']) ? 'https' : 'http'
	)
);

$configuration = (new Configuration\ApplicationConfiguration())->read();

$redis = new Predis\Client($configuration['REDIS']['uri']);

echo (new class(
	$configuration,
	new Routing\MatchingRoutes(
		new Bulletpoint\Routing\NginxMatchedRoutes(
			new Bulletpoint\Routing\ApplicationRoutes(
				new Storage\CachedConnection(
					new Storage\PDOConnection(
						new Storage\SafePDO(
							$configuration['DATABASE']['dsn'],
							$configuration['DATABASE']['user'],
							$configuration['DATABASE']['password']
						)
					),
					$redis
				),
				$uri,
				new Encryption\PasswordHash()
			)
		),
		$uri,
		$_SERVER['REQUEST_METHOD']
	),
	new Tracy\Logger(__DIR__ . '/../logs')
) implements Output\Template {
	/** @var mixed[] */
	private $configuration;

	/** @var \Klapuch\Routing\Routes */
	private $routes;

	/** @var \Tracy\ILogger */
	private $logger;

	public function __construct(array $configuration, Routing\Routes $routes, Tracy\ILogger $logger) {
		$this->configuration = $configuration;
		$this->routes = $routes;
		$this->logger = $logger;
	}

	public function render(array $variables = []): string {
		try {
			$match = $this->routes->matches();
			/** @var \Closure $destination */
			$destination = current($match);
			return (new Application\RawTemplate(
				$destination()->response(
					(new Routing\TypedMask(
						new Routing\CombinedMask(
							new Bulletpoint\Routing\NginxMask(),
							new Bulletpoint\Routing\CommonMask()
						)
					))->parameters()
				)
			))->render();
		} catch (\Throwable $e) {
			$this->logger->log($e);
			if ($e instanceof \UnexpectedValueException) {
				return (new Application\RawTemplate(
					new Bulletpoint\Response\JsonError($e)
				))->render();
			}
			return (new Application\RawTemplate(
				new Bulletpoint\Response\JsonError(
					new \UnexpectedValueException(),
					[],
					HTTP_INTERNAL_SERVER_ERROR
				)
			))->render();
		}
	}
})->render();
