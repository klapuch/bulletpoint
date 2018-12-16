<?php declare(strict_types=1);

function t(string $identifier, string ...$params): string {
	$translations = [
		'access.bad.email' => ['cs' => 'Email "%s" neexistuje', 'en' => 'Email "%s" does not exist'],
		'access.bad.password' => ['cs' => 'Špatné heslo', 'en' => 'Wrong password'],
	];
	return vsprintf($translations[$identifier][PHP_SAPI === 'cli' ? 'en' : 'cs'], $params);
}
