<?php
declare(strict_types = 1);
require __DIR__ . '/vendor/autoload.php';
use Klapuch\Log;
const LOGS = __DIR__ . '/Tests/Temporary';
try {
	throw new \Exception('Exception!!!');
} catch(\Throwable $ex) {
	(new Log\FilesystemLogs(
		new Log\DynamicLocation(new Log\DirectoryLocation(LOGS))
	))->put(
		new Log\PrettyLog(
			$ex,
			new Log\PrettySeverity(
				new Log\JustifiedSeverity(Log\Severity::ERROR)
			)
		)
	);
}