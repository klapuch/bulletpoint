<?php
namespace Bulletpoint\Page;

use Bulletpoint\Model\{
    Access, Constraint, User, Translation, Conversation, Wiki
};
use Bulletpoint\Exception;
use Bulletpoint\Component;
use Nette\Http\IResponse;

final class ProfilPage extends BasePage {
    /**
     * @var \Bulletpoint\Model\User\Profile
     */
    private $profile;
    /**
     * @var \Bulletpoint\Model\Access\Identity
     */
    private $owner;

    public function startup() {
        parent::startup();
        try {
            (new Constraint\UsernameExistenceRule($this->database))
                ->isSatisfied($this->getParameter('username'));
            $this->profile = new User\MySqlProfile(
                new Access\MySqlIdentity(
                    (new Translation\MySqlUsernameSlug(
                        $this->getParameter('username'),
                        $this->database
                    ))->origin(),
                    $this->database
                ),
                $this->database
            );
            $this->owner = $this->profile->owner();
        } catch(Exception\ExistenceException $ex) {
            $this->error(
                'Uživatel neexistuje',
                IResponse::S404_NOT_FOUND
            );
        }
    }

    public function renderDefault(string $username) {
        $this->template->username = $username;
        $owner = $this->profile->owner();
        $this->template->comments = new Conversation\OwnedMySqlComments(
            $owner,
            $this->database
        );
        $this->template->bulletpoints = new Wiki\OwnedMySqlBulletpoints(
            $owner,
            $this->database
        );
        $this->template->documents = new Wiki\OwnedMySqlDocuments(
            $owner,
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
        return new Component\Role($this->profile, $this->identity);
    }

    protected function createComponentProfilePhoto() {
        return new Component\ProfilePhoto($this->owner);
    }
}