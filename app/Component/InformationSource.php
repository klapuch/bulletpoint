<?php
namespace Bulletpoint\Component;

use Bulletpoint\Model\Wiki;
use Nette\Application\UI;

final class InformationSource extends BaseControl {
    private $informationSource;

    public function __construct(Wiki\InformationSource $informationSource) {
        parent::__construct();
        $this->informationSource = $informationSource;
    }

    public function render() {
        $this->template->setFile(__DIR__ . '/InformationSource.latte');
        $this->template->backlink = $this->presenter->storeRequest();
        $this->template->informationSource = $this->informationSource;
        $this->template->render();
    }
}