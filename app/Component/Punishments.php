<?php
namespace Bulletpoint\Component;

use Bulletpoint\Model\{
    Constraint, Access, Storage
};
use Nette\Application\UI;

final class Punishments extends BaseControl {
    private $punishments;
    private $identity;
    private $database;

    public function __construct(
        Constraint\Punishments $punishments,
        Access\Identity $identity,
        Storage\Database $database
    ) {
        parent::__construct();
        $this->punishments = $punishments;
        $this->identity = $identity;
        $this->database = $database;
    }

    public function render() {
        $this->template->setFile(__DIR__ . '/Punishments.latte');
        $this->template->punishments = $this->punishments->iterate();
        $this->template->render();
    }

    protected function createComponentPunishment() {
        $components = [];
        foreach($this->punishments->iterate() as $punishment) {
            $components[$punishment->id()] = new Punishment(
                $punishment,
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

