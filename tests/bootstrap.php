<?php
declare(strict_types = 1);
use Bulletpoint\Core\Control;

require __DIR__ . '/../home/app/Core/Control/AutoLoader.php';
require __DIR__ . '/../home/vendor/autoload.php';

spl_autoload_register([
	new Control\AutoLoader([__DIR__ . '/../home/app', __DIR__]),
	'load'
]);

Tester\Environment::setup();
date_default_timezone_set('Europe/Prague');