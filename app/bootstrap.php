<?php

require __DIR__ . '/../vendor/autoload.php';

$configurator = new Nette\Configurator;

$configurator->setDebugMode(true);
$configurator->enableDebugger(__DIR__ . '/../log');
$configurator->setTempDirectory(__DIR__ . '/../temp');

$configurator->createRobotLoader()
    ->addDirectory(__DIR__)
    ->register();

$configurator->addConfig(__DIR__ . '/Config/config.neon');
$configurator->addConfig(__DIR__ . '/Config/config.local.neon', Nette\Configurator::AUTO);
$container = $configurator->createContainer();

header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('X-XSS-Protection: 1; mode=block; report=https://report-uri.io/report/bulletpoint');
header('X-Powered-By: ');
header("Content-Security-Policy: default-src 'self' ; script-src 'self' ; style-src 'self' ; img-src 'self' data: ; font-src 'self' https://fonts.gstatic.com; connect-src 'self' ; media-src 'none' ; object-src 'none' ; child-src 'none' ; frame-ancestors 'none' ; form-action 'self' ; upgrade-insecure-requests; block-all-mixed-content; report-uri https://report-uri.io/report/bulletpoint;");

return $container;
