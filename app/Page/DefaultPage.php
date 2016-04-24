<?php
namespace Bulletpoint\Page;

use Nette\Application\UI;
use Bulletpoint\Component;

class DefaultPage extends BasePage {
    protected function createComponentSearchForm() {
        $form = new Component\BaseForm();
        $form->addText('keyword')
            ->setType('search')
            ->addRule(UI\Form::FILLED, 'Zadej hledaný výraz');
        $form->onSuccess[] = function(UI\Form $form) {
            $this->searchFormSucceeded($form);
        };
        return $form;
    }

    public function searchFormSucceeded(UI\Form $form) {
        $this->redirect('Hledani:default', $form->values->keyword);
    }
}
