<?php

require __DIR__ . '/../vendor/autoload.php';

$configurator = new Nette\Configurator;
$configurator->setDebugMode(true);
$configurator->enableDebugger(__DIR__ . '/../log');
$configurator->setTempDirectory(__DIR__ . '/../temp');

$configurator->addConfig(
    __DIR__ . '/Config/config.neon',
    Nette\Configurator::AUTO
);
$configurator->addConfig(
    __DIR__ . '/Config/config.local.neon',
    Nette\Configurator::AUTO
);
$container = $configurator->createContainer();

return $container;