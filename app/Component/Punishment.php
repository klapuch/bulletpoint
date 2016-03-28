<?php
namespace Bulletpoint\Component;

use Bulletpoint\Exception;
use Bulletpoint\Model\{
    Constraint, Access, User, Storage
};
use Nette\Application\UI;

final class Punishment extends BaseControl {
    private $punishment;
    private $identity;
    private $database;

    public function __construct(
        Constraint\Punishment $punishment,
        Access\Identity $identity,
        Storage\Database $database
    ) {
        parent::__construct();
        $this->punishment = $punishment;
        $this->identity = $identity;
        $this->database = $database;
    }

    public function render() {
        $this->template->setFile(__DIR__ . '/Punishment.latte');
        $this->template->identity = $this->identity;
        $this->template->owner = $this->punishment->sinner();
        $this->template->punishment = $this->punishment;
        $this->template->render();
    }
    
    public function renderMinimal() {
        $this->template->setFile(__DIR__ . '/Punishment.Minimal.latte');
        $this->template->punishment = $this->punishment;
        $this->template->render();
    }

    /**
     * @secured
     */
    public function handleZrusit() {
        $this->punishment->forgive();
        $this->presenter->flashMessage('Uživatel je odblokován', 'success');
        $this->presenter->redirect('this');
    }

    protected function createComponentPunishmentForm() {
        return new PunishmentForm(
            new Constraint\ActualMySqlPunishments(
                $this->identity,
                $this->database
            ),
            $this->punishment->sinner()
        );
    }
}