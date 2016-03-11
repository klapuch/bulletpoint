<?php

function isArray($value): bool {
	return (array)$value === $value;
}

function dd($dump = null) {
	exit(d($dump));
}

function d($dump) {
	var_dump($dump);
}

function regenerateSession(int $elapse = 20, string $name = 'session_timer') {
	if(isset($_SESSION[$name]) && (time() - $_SESSION[$name]) > $elapse) {
		$_SESSION[$name] = time();
		session_regenerate_id(true);
	} elseif(!isset($_SESSION[$name]))
		$_SESSION[$name] = time();
}

function isLocalhost(string $address): bool {
	return $address == '::1'
	|| $address == '127.0.0.1'
	|| $address == 'localhost';
}

/**
* https://secure.php.net/manual/en/function.memory-get-usage.php#96280
*/
function convert($size) {
    $unit = ['b', 'kb', 'mb', 'gb', 'tb', 'pb'];
    return @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $unit[$i];
}