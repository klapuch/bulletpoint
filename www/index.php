<?php
declare(strict_types = 1);

if(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    $_SERVER['SERVER_PORT'] = 443;

$container = require __DIR__ . '/../app/bootstrap.php';
$container->getByType('Nette\Application\Application')->run();