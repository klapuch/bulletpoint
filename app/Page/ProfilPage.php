<?php
namespace Bulletpoint\Page;

use Bulletpoint\Model\{
    Access, Constraint, Translation, Conversation, Wiki
};
use Bulletpoint\Exception;
use Bulletpoint\Component;
use Nette\Http\IResponse;
use Nette\Caching\Storages;

final class ProfilPage extends BasePage {
    /**
     * @var \Bulletpoint\Model\Access\Identity
     */
    private $owner;

    public function startup() {
        parent::startup();
        try {
            (new Constraint\UsernameExistenceRule($this->database))
                ->isSatisfied($this->getParameter('username'));
            $this->owner = new Access\CachedIdentity(
                new Access\MySqlIdentity(
                    (new Translation\MySqlUsernameSlug(
                        $this->getParameter('username'),
                        $this->database
                    ))->origin(),
                    $this->database
                ),
                new Storages\MemoryStorage
            );
        } catch(Exception\ExistenceException $ex) {
            $this->error(
                'Uživatel neexistuje',
                IResponse::S404_NOT_FOUND
            );
        }
    }

    public function renderDefault(string $username) {
        $this->template->username = $username;
        $this->template->comments = new Conversation\OwnedMySqlComments(
            $this->owner,
            $this->database
        );
        $this->template->bulletpoints = new Wiki\OwnedMySqlBulletpoints(
            $this->owner,
            $this->database
        );
        $this->template->documents = new Wiki\OwnedMySqlDocuments(
            $this->owner,
            $this->database
        );
    }
    
    protected function createComponentPunishment() {
        return new Component\Punishment(
            (new Constraint\OwnedMySqlPunishments(
                $this->owner,
                $this->database,
                new Constraint\ActualMySqlPunishments(
                    $this->identity,
                    $this->database
                )
            ))->iterate()->current(),
            $this->identity,
            $this->database
        );
    }

    protected function createComponentRole() {
        return new Component\Role($this->owner, $this->identity);
    }

    protected function createComponentProfilePhoto() {
        return new Component\ProfilePhoto($this->owner);
    }
}