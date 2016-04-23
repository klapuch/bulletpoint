<?php
namespace Bulletpoint\TestCase;

use Nette\DI;

abstract class Page extends \Tester\TestCase {
    use \Testbench\TPresenter;

    protected $container;

    public function __construct(DI\Container $container) {
        $this->container = $container;
    }

    public function setUp() {
        parent::setUp();
        \Tester\Environment::lock('page', __DIR__ . '/../temp');
        $this->logOut();
        $this->container
            ->getByType('Bulletpoint\Model\Storage\Database')
            ->exec(file_get_contents(__DIR__ . '/../../dataset.sql'));
    }
}