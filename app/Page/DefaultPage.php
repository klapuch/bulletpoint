<?php
namespace Bulletpoint\Page;

use Nette\Application\UI;

class DefaultPage extends BasePage {
    protected function createComponentSearchForm() {
        $form = new UI\Form();
        $form->setMethod('GET')
            ->setAction($this->link('Hledani:'));
        $form->addText('keyword')
            ->setType('search')
            ->addRule(UI\Form::FILLED, 'Zadej hledaný výraz');
        return $form;
    }
}
