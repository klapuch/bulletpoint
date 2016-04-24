<?php
namespace Bulletpoint\Component;

use Bulletpoint\Model\{
    Constraint, Access
};
use Nette\Application\UI;

final class PunishmentForm extends BaseControl {
    private $punishments;
    private $sinner;

    public function __construct(
        Constraint\Punishments $punishments,
        Access\Identity $sinner
    ) {
        parent::__construct();
        $this->punishments = $punishments;
        $this->sinner = $sinner;
    }

    public function render() {
        $this->template->setFile(__DIR__ . '/PunishmentForm.latte');
        $this->template->render();
    }

    public function createComponentForm() {
        $form = new BaseForm();
        
        $reasons = ['Nevhodné chování', 'Sprosté vyjadřování'];
        $form->addSelect(
            'reason',
            'Důvod',
            array_combine($reasons, $reasons)
        );
        $form->addText('expiration', 'Expirace')
            ->addRule(UI\Form::FILLED, '%label musí být vyplněna')
            ->addRule(
                function($control) {
                    return date('d.m.Y H:i', strtotime($control->value))
                    == $control->value;
                },
                'Expirace musí být datum ve formátu dd.mm.Y hh:mm'
            )
            ->addRule(
                function($control) {
                    return new \DateTimeImmutable($control->value) > new \DateTimeImmutable();
                },
                'Trest smí být udělen pouze na budoucí období'
            );
        $form->addSubmit('punish', 'Potrestat');
        $form->onSuccess[] = function(UI\Form $form) {
            $this->formSucceeded($form);
        };
        return $form;
    }

    protected function formSucceeded(UI\Form $form) {
        try {
            $values = $form->values;
            $this->punishments->punish(
                $this->sinner,
                new \DateTimeImmutable($values->expiration),
                $values->reason
            );
            $this->presenter->flashMessage('Trest byl udělen', 'success');
            $this->presenter->redirect('this');
        } catch(\LogicException $ex) {
            $form->addError($ex->getMessage());
        }
    }
}
