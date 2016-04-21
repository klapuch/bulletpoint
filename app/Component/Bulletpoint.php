<?php
namespace Bulletpoint\Component;

use Bulletpoint\Model\{
    Wiki, Access, Storage, Rating
};

final class Bulletpoint extends BaseControl {
    private $bulletpoint;
    private $rating;
    private $myself;
    private $database;

    public function __construct(
        Wiki\Bulletpoint $bulletpoint,
        Rating\Rating $rating,
        Access\Identity $myself,
        Storage\Database $database
    ) {
        parent::__construct();
        $this->bulletpoint = $bulletpoint;
        $this->rating = $rating;
        $this->myself = $myself;
        $this->database = $database;
    }

    public function render() {
        $this->template->setFile(__DIR__ . '/Bulletpoint.latte');
        $this->template->bulletpoint = $this->bulletpoint;
        $this->template->backlink = $this->presenter->storeRequest();
        $this->template->rating = $this->rating;
        $this->template->myself = $this->myself;
        if($this->presenter->isAjax()) {
            $this->template->rating = new Rating\MySqlBulletpointRating(
                $this->bulletpoint,
                $this->myself,
                $this->database
            );
        }
        $this->template->render();
    }

    /**
     * @secured
     */
    public function handlePro() {
        $this->rate('+');
    }

    /**
     * @secured
     */
    public function handleProti() {
        $this->rate('-');
    }

    protected function createComponentInformationSource() {
        return new InformationSource($this->bulletpoint->source());
    }

    private function rate(string $type) {
        $type === '+' ? $this->rating->increase() : $this->rating->decrease();
        if(!$this->presenter->isAjax()) {
            $this->presenter->flashMessage('Ohodnoceno', 'success');
            $this->presenter->redirect('this');
        }
        $this->redrawControl();
    }
}