<?php declare(strict_types=1);

function t(string $identifier, string ...$params): string {
	$translations = [
		'access.bad.email' => ['cs' => 'Email "%s" neexistuje', 'en' => 'Email "%s" does not exist'],
		'access.bad.password' => ['cs' => 'Špatné heslo', 'en' => 'Wrong password'],
		'access.bad.username' => ['cs' => 'Uživatelské jméno "%s" neexistuje', 'en' => 'Username "%s" does not exist'],
		'bulletpoint.source.link.not.valid' => ['cs' => 'Zdrojová URL není platná', 'en' => 'URL of source is not valid'],
		'error.unknown' => ['cs' => 'Došlo k neznámé chybě, zkuste to prosím znovu.', 'en' => 'Unknown error, contact support.'],
		'response.not.allowed' => ['cs' => 'Nedostatečné oprávnění.', 'en' => 'You are not allowed to see the response.'],
		'tag.already.exists' => ['cs' => 'Tag "%s" již existuje.', 'en' => 'Tag "%s" already exists.'],
		'theme.reference.url.not.valid' => ['cs' => 'Odkaz není platná URL', 'en' => 'URL of reference is not valid'],
	];
	return vsprintf($translations[$identifier][PHP_SAPI === 'cli' ? 'en' : 'cs'], $params);
}
