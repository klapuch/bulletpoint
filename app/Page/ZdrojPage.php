<?php
namespace Bulletpoint\Page;

use Bulletpoint\Model\{
    Access, Constraint, Wiki, Security, Filesystem
};
use Bulletpoint\Exception;
use Bulletpoint\Component;
use Nette\Http\IResponse;
use Nette\Application\UI;

final class ZdrojPage extends BasePage {
    /** @persistent */
    public $backlink;

    public function actionUpravit(int $id) {
        try {
            (new Constraint\InformationSourceExistenceRule($this->database))
                ->isSatisfied($id);
            $source = new Wiki\MySqlInformationSource(
                $id,
                $this->database
            );
            $this['informationSourceForm']['source']->defaults = [
                'place' => $source->place(),
                'year' => $source->year(),
                'author' => $source->author(),
            ];
        } catch(Exception\ExistenceException $ex) {
            $this->error(
                'Zdroj neexistuje',
                IResponse::S404_NOT_FOUND
            );
        }
    }

    protected function createComponentInformationSourceForm() {
        $form = new Component\BaseForm();
        $form->addComponent(
            new Component\InformationSourceContainer,
            'source'
        );
        $form->onSuccess[] = function(UI\Form $form) {
            $this->informationSourceFormSucceeded($form);
        };
        return $form;
    }

    public function informationSourceFormSucceeded(UI\Form $form) {
        $values = $form->values->source;
        (new Wiki\MySqlInformationSource(
            $this->getParameter('id'),
            $this->database
        ))->edit(
            $values->place,
            $values->year,
            $values->author
        );
        $this->flashMessage('Zdroj byl upraven', 'success');
        $this->restoreRequest($this->backlink);
        $this->redirect('this');
    }
}