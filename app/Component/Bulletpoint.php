<?php
namespace Bulletpoint\Component;

use Bulletpoint\Model\{
    Wiki, Access, Storage, Rating
};

final class Bulletpoint extends BaseControl {
    private $bulletpoint;
    private $identity;
    private $database;

    public function __construct(
        Wiki\Bulletpoint $bulletpoint,
        Access\Identity $identity,
        Storage\Database $database
    ) {
        parent::__construct();
        $this->bulletpoint = $bulletpoint;
        $this->identity = $identity;
        $this->database = $database;
    }

    public function render() {
        $this->template->setFile(__DIR__ . '/Bulletpoint.latte');
        $this->template->bulletpoint = $this->bulletpoint;
        $this->template->backlink = $this->presenter->storeRequest();
        $this->template->rating = $this->rating();
        $this->template->userRating = $this->userRating();
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
        $rating = new Rating\MySqlBulletpointRating(
            $this->bulletpoint,
            $this->identity,
            $this->database
        );
        $type === '+' ? $rating->increase() : $rating->decrease();
        if(!$this->presenter->isAjax()) {
            $this->presenter->flashMessage('Ohodnoceno', 'success');
            $this->presenter->redirect('this');
        }
        $this->redrawControl();
    }

    private function rating() {
        return new Rating\MySqlBulletpointRating(
            $this->bulletpoint,
            $this->identity,
            $this->database
        );
    }

    private function userRating() {
        return new Rating\MySqlUserBulletpointRating(
            $this->bulletpoint,
            $this->identity,
            $this->database,
            $this->rating()
        );
    }
}