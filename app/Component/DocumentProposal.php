<?php
namespace Bulletpoint\Component;

use Bulletpoint\Exception;
use Bulletpoint\Model\{
    Wiki, Storage, Translation, Text
};
use Nette\Utils\Strings;
use Texy;

final class DocumentProposal extends BaseControl {
    private $proposal;
    private $database;

    public function __construct(
        Wiki\DocumentProposal $proposal,
        Storage\Database $database
    ) {
        parent::__construct();
        $this->proposal = $proposal;
        $this->database = $database;
    }

    public function render() {
        $this->template->setFile(__DIR__ . '/DocumentProposal.latte');
        $this->template->id = $this->proposal->id();
        $this->template->username = $this->proposal->author()->username();
        $this->template->description = $this->proposal->description();
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
                        $acceptedProposal = $this->proposal->accept();
                        (new Translation\MySqlDocumentSlugs(
                            $this->database
                        ))->add(
                            $acceptedProposal->id(),
                            Strings::webalize($acceptedProposal->title())
                        );
                    }
                );
            $this->presenter->flashMessage('Návrh byl přijat', 'success');
            $this->presenter->redirect('Navrhy:Dokumenty');
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
        $this->presenter->redirect('Navrhy:Dokumenty');
    }

    protected function createComponentInformationSource() {
        return new InformationSource($this->proposal->source());
    }

    protected function createComponentProfilePhoto() {
        return new ProfilePhoto($this->proposal->author());
    }
}