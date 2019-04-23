<?php
declare(strict_types = 1);

function t(string $identifier, string ...$params): string {
	$translations = [
		'access.username.exists' => ['cs' => 'Uživatelské jméno "%s" již existuje.', 'en' => 'Username "%s" already exists.'],
		'access.bad.email' => ['cs' => 'Email "%s" neexistuje', 'en' => 'Email "%s" does not exist'],
		'access.bad.password' => ['cs' => 'Špatné heslo', 'en' => 'Wrong password'],
		'access.bad.username' => ['cs' => 'Uživatelské jméno "%s" neexistuje', 'en' => 'Username "%s" does not exist'],
		'bulletpoint.source.link.not.valid' => ['cs' => 'Zdrojová URL není platná', 'en' => 'URL of source is not valid'],
		'error.unknown' => ['cs' => 'Došlo k neznámé chybě, zkuste to prosím znovu.', 'en' => 'Unknown error, contact support.'],
		'response.not.allowed' => ['cs' => 'Nedostatečné oprávnění.', 'en' => 'You are not allowed to see the response.'],
		'tag.already.exists' => ['cs' => 'Tag "%s" již existuje.', 'en' => 'Tag "%s" already exists.'],
		'theme.reference.url.not.valid' => ['cs' => 'Odkaz není platná URL', 'en' => 'URL of reference is not valid'],
		'avatars.file.not.successfully.uploaded' => ['cs' => 'Soubor nebyl úspěšně nahrán', 'en' => 'File was not successfully uploaded'],
		'avatars.file.not.image' => ['cs' => 'Soubor není obrázek', 'en' => 'File is not an image'],
	];
	return vsprintf($translations[$identifier][PHP_SAPI === 'cli' ? 'en' : 'cs'], $params);
}
