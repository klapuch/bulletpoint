<?php
namespace Bulletpoint\Component;

use Bulletpoint\Model\{
    Wiki, Access, Storage, Rating
};
use Nette\Application\UI;

final class Bulletpoints extends BaseControl {
    private $bulletpoints;
    private $identity;
    private $database;

    public function __construct(
        Wiki\Bulletpoints $bulletpoints,
        Access\Identity $identity,
        Storage\Database $database
    ) {
        parent::__construct();
        $this->bulletpoints = $bulletpoints;
        $this->identity = $identity;
        $this->database = $database;
    }

    public function render() {
        $this->template->setFile(__DIR__ . '/Bulletpoints.latte');
        $this->template->bulletpoints = $this->bulletpoints->iterate();
        $this->template->render();
    }

    protected function createComponentBulletpoints() {
        $components = [];
        foreach($this->bulletpoints->iterate() as $bulletpoint) {
            $components[$bulletpoint->id()] = new Bulletpoint(
                $bulletpoint,
                $this->identity,
                $this->database
            );
        }
        return new UI\Multiplier(
            function($id) use ($components) {
                return $components[$id];
            }
        );
    }
}