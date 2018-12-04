<?php
declare(strict_types = 1);

require __DIR__ . '/../vendor/autoload.php';

use Bulletpoint\Configuration;
use Klapuch\Application;
use Klapuch\Output;
use Klapuch\Routing;
use Klapuch\Storage;
use Klapuch\Uri;

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
				)
			)
		),
		$uri,
		$_SERVER['REQUEST_METHOD']
	)
) implements Output\Template {
	/** @var mixed[] */
	private $configuration;

	/** @var \Klapuch\Routing\Routes */
	private $routes;

	public function __construct(
		array $configuration,
		Routing\Routes $routes
	) {
		$this->configuration = $configuration;
		$this->routes = $routes;
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
			throw $e;
		}
	}
})->render();
