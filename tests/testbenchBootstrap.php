<?php
declare(strict_types = 1);

require __DIR__ . '/../vendor/autoload.php';

Testbench\Bootstrap::setup(
    __DIR__ . '/temp_testbench',
    function(Nette\Configurator $configurator) {
        $configurator->createRobotLoader()
            ->addDirectory(__DIR__)
            ->register();
        $configurator->addConfig(
            __DIR__ . '/../app/config/config.neon',
            'test'
        );
        $configurator->addParameters(['appDir' => __DIR__ . '/../app']);
    }
);