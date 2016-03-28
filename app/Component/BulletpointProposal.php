<?php
namespace Bulletpoint\Component;

use Bulletpoint\Exception;
use Bulletpoint\Model\{
    Wiki, Storage
};

final class BulletpointProposal extends BaseControl {
    private $proposal;
    private $database;

    public function __construct(
        Wiki\BulletpointProposal $proposal,
        Storage\Database $database
    ) {
        parent::__construct();
        $this->proposal = $proposal;
        $this->database = $database;
    }

    public function render() {
        $this->template->setFile(__DIR__ . '/BulletpointProposal.latte');
        $this->template->proposal = $this->proposal;
        $this->template->backlink = $this->presenter->storeRequest();
        $this->template->render();
    }

    /**
     * @secured
     */
    public function handlePrijmout() {
        try {
            (new Storage\Transaction($this->database))
                ->start(
                    function() {
                        $this->proposal->accept();
                    }
                );
            $this->presenter->flashMessage('Návrh byl přijat', 'success');
            $this->presenter->redirect('Navrhy:Bulletpointy');
        } catch(Exception\DuplicateException $ex) {
            $this->presenter->flashMessage($ex->getMessage(), 'danger');
            $this->presenter->redirect('this');
        } catch(Exception\StorageException $ex) {
            $this->presenter->flashMessage($ex->getMessage(), 'danger');
            $this->presenter->redirect('this');
        }
    }

    /**
     * @secured
     */
    public function handleOdmitnout(string $reason = null) {
        $this->proposal->reject($reason);
        $this->presenter->flashMessage('Návrh byl odmítnut', 'success');
        $this->presenter->redirect('Navrhy:Bulletpointy');
    }

    protected function createComponentInformationSource() {
        return new InformationSource($this->proposal->source());
    }

    protected function createComponentProfilePhoto() {
        return new ProfilePhoto($this->proposal->author());
    }
}