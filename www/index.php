<?php
declare(strict_types = 1);

use Bulletpoint\Core\{
	Control,
	Filesystem,
	Security,
	Http,
	Reflection,
	Text,
	UI,
	Storage,
	Access
};
use Bulletpoint\Page;
use Bulletpoint\Exception;

// require __DIR__ . '/.maintance.php';
header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('X-XSS-Protection: 1; mode=block; report=https://report-uri.io/report/bulletpoint');
header("Content-Security-Policy: default-src 'self' ; script-src 'self' ; style-src 'self' ; img-src 'self' data: ; font-src 'self' https://fonts.gstatic.com; connect-src 'none' ; media-src 'none' ; object-src 'none' ; child-src 'none' ; frame-ancestors 'none' ; form-action 'self' ; upgrade-insecure-requests; block-all-mixed-content; report-uri https://report-uri.io/report/bulletpoint;");
mb_internal_encoding('UTF-8');
require __DIR__ . '/../home/vendor/autoload.php';
require __DIR__ . '/../home/functions.php';
require __DIR__ . '/../home/app/Core/Control/AutoLoader.php';
spl_autoload_register([new Control\AutoLoader([__DIR__ . '/../home/app']), 'load']);
$config = __DIR__ . '/../home/app/config/.config.ini';
if(isLocalhost($_SERVER['SERVER_ADDR']))
	$config = __DIR__ . '/../home/app/config/.config.local.ini';
$ini = new Control\IniConfiguration($config);
session_set_save_handler(
	new Security\FilesystemSession($ini->toSection('cryptography')->session)
);

session_start();
regenerateSession();

/*register_shutdown_function(
	function() {
		(new Control\FilesystemLog(
			new Filesystem\ExistingPath(
				new Filesystem\StandardizedPath(
					__DIR__ . '/../home/app/logs/error.txt'
				)
			)
		))->write((array)error_get_last());
	}
);*/

define('VIEW_PATH', __DIR__ . '/../home/app/View/');
define('PAGE_PATH', __DIR__ . '/../home/app/Page/');
define('TEMP_DIR', __DIR__ . '/../tmp/');
$url = new Http\CachedAddress(
	new Http\Url(
		$_SERVER['SCRIPT_NAME'],
		$_SERVER['REQUEST_URI']
	), 
	new Storage\RuntimeCache
);

try {
	$page = (new Page\FrontPage(
		new Http\Request($_GET, $_POST, $url),
		new UI\LatteTemplate(
			new Filesystem\SprintfPath(
				new Filesystem\ExistingPath(
					new Filesystem\StandardizedPath(VIEW_PATH, '', 'latte')
				)
			),
			(new Latte\Engine)
			->setTempDirectory(TEMP_DIR)
			->addFilter('webalize', function($string) {
				return (new Text\WebalizedCorrection)->replacement($string);
			}),
			new Http\FullyRoutedView(
				new Filesystem\SprintfPath(
					new Filesystem\ExistingPath(
						new Filesystem\StandardizedPath(VIEW_PATH, '', 'latte')
					)
				),
				new Http\CorrectlyRoutedView(
					new Http\SimplyRoutedView($url),
					new Text\LowerCaseCorrection
				),
				new Http\CorrectlyRoutedPage(
					new Http\SimplyRoutedPage($url),
					new Text\CorrectionChain(
						new Text\PascalCaseCorrection,
						new Text\FirstUpperCaseCorrection
					)
				)
			)
		),
		new Http\BasicRouter(
			new Http\CorrectlyRoutedPage(
				new Http\ReliablyRoutedPage(
					new Filesystem\SprintfPath(
						new Filesystem\ExistingPath(
							new Filesystem\StandardizedPath(PAGE_PATH, '', 'php')
						)
					),
					new Http\CorrectlyRoutedPage(
						new Http\SimplyRoutedPage($url),
						new Text\CorrectionChain(
							new Text\PascalCaseCorrection,
							new Text\SuffixCorrection('Page')
						)
					)
				),
				new Text\PrefixCorrection('Bulletpoint\\Page\\')
			),
			new Http\ReliablyRoutedView(
				new Filesystem\SprintfPath(
					new Filesystem\ExistingPath(
						new Filesystem\StandardizedPath(VIEW_PATH, '', 'latte')
					)
				),
				new Http\CorrectlyRoutedView(
					new Http\SimplyRoutedView($url),
					new Text\LowerCaseCorrection
				),
				new Http\CorrectlyRoutedPage(
					new Http\SimplyRoutedPage($url),
					new Text\CorrectionChain(
						new Text\PascalCaseCorrection,
						new Text\FirstUpperCaseCorrection
					)
				)
			),
			new Http\RoutedParameter($url)
		),
		new Reflection\Request(
			$_POST,
			new Http\CorrectlyRoutedView(
				new Http\SimplyRoutedView($url),
					new Text\CorrectionChain(
						new Text\PascalCaseCorrection,
						new Text\FirstUpperCaseCorrection
					)
			),
			new ReflectionClass(
				(new Http\CorrectlyRoutedPage(
					new Http\ReliablyRoutedPage(
						new Filesystem\SprintfPath(
							new Filesystem\ExistingPath(
								new Filesystem\StandardizedPath(PAGE_PATH, '', 'php')
							)
						),
						new Http\CorrectlyRoutedPage(
							new Http\SimplyRoutedPage($url),
							new Text\CorrectionChain(
								new Text\PascalCaseCorrection,
								new Text\SuffixCorrection('Page')
							)
						)
					),
					new Text\PrefixCorrection('Bulletpoint\\Page\\')
				))->page()
			)
		),
		$ini
	))->load();
} catch(\Throwable $ex) {
	$code = $ex->getCode() ? $ex->getCode() : 404;
	header('Location: ' . $url->basename() . 'chyba/' . $code);
	exit;
}