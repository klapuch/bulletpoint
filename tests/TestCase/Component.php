<?php
namespace Bulletpoint\TestCase;

use Nette\DI;

abstract class Component extends \Tester\TestCase {
    use \Testbench\TComponent;

    protected $container;

    public function __construct(DI\Container $container) {
        $this->container = $container;
    }

    public function setUp() {
        parent::setUp();
        \Tester\Environment::lock('component', __DIR__ . '/../temp');
        $this->container
            ->getByType('Bulletpoint\Model\Storage\Database')
            ->exec(file_get_contents(__DIR__ . '/../../dataset.sql'));
    }
}