<?php
namespace Bulletpoint\Component;

use Bulletpoint\Model\Wiki;

final class InformationSource extends BaseControl {
    private $informationSource;

    public function __construct(Wiki\InformationSource $informationSource) {
        parent::__construct();
        $this->informationSource = $informationSource;
    }

    public function render() {
        $this->template->setFile(__DIR__ . '/InformationSource.latte');
        $this->template->backlink = $this->presenter->storeRequest();
        $this->template->place = $this->informationSource->place();
        $this->template->year = $this->informationSource->year();
        $this->template->author = $this->informationSource->author();
        $this->template->id = $this->informationSource->id();
        $this->template->render();
    }
}