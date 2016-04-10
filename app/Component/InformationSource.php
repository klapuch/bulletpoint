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
        $this->template->backlink = $this->presenter->storeRequest('+45 minutes');
        $this->template->source = $this->informationSource;
        $this->template->render();
    }
}