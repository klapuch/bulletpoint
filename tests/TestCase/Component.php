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
        $this->logOut();
        \Tester\Environment::lock('component', __DIR__ . '/../temp');
        $this->container
            ->getByType('Bulletpoint\Model\Storage\Database')
            ->exec(file_get_contents(__DIR__ . '/../../dataset.sql'));
    }

    protected function logIn($id = 1, $roles = null, $data = null) {
        if($id instanceof \Nette\Security\IIdentity) {
            $identity = $id;
        } else {
            $identity = new \Nette\Security\Identity($id, $roles, $data);
        }
        /** @var \Nette\Security\User $user */
        $user = \Testbench\ContainerFactory::create(false)->getByType(
            'Nette\Security\User'
        );
        $user->login($identity);
        return $user;
    }

    protected function logOut() {
        /** @var \Nette\Security\User $user */
        $user = \Testbench\ContainerFactory::create(false)->getByType(
            'Nette\Security\User'
        );
        $user->logout();
        return $user;
    }
}