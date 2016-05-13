<?php
declare(strict_types = 1);

require __DIR__ . '/../vendor/autoload.php';

Tester\Environment::setup();
date_default_timezone_set('Europe/Prague');

$configurator = new Nette\Configurator;
$configurator->setDebugMode(false);
$configurator->setTempDirectory(__DIR__ . '/temp');
$configurator->addConfig(__DIR__ . '/../app/config/config.neon', 'test');
$configurator->createRobotLoader()
    ->addDirectory(__DIR__ . '/Fake')
    ->addDirectory(__DIR__ . '/TestCase')
    ->register();

Testbench\Bootstrap::setup(
    __DIR__ . '/temp_testbench',
    function(Nette\Configurator $configurator) {
        $configurator->addConfig(
            __DIR__ . '/../app/config/config.neon',
            'test'
        );
    }
);

return $configurator->createContainer();