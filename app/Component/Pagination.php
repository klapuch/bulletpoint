<?php
namespace Bulletpoint\Component;

use Nette\Utils;

final class Pagination extends BaseControl {
    private $pagination;

    public function __construct(Utils\Paginator $pagination) {
        parent::__construct();
        $this->pagination = $pagination;
    }

    public function render() {
        $this->template->setFile(__DIR__ . '/Pagination.latte');
        $this->pagination->page = $this->pagination->base;
        if(isset($this->parameters['strana']))
            $this->pagination->page = $this->parameters['strana'];
        if($this->outOfRange())
            $this->presenter->error('Strana je mimo povolený rozsah');
        $this->template->pagination = $this->pagination;
        $this->template->render();
    }

    private function outOfRange(): bool {
        return $this->pagination->page < $this->pagination->firstPage
        || $this->pagination->page > $this->pagination->lastPage;
    }
}