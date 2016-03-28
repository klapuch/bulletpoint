<?php
namespace Bulletpoint\Page;

use Bulletpoint\Model\{
    Access, Constraint, User, Translation
};
use Bulletpoint\Exception;
use Bulletpoint\Component;
use Nette\Http\IResponse;

final class ProfilPage extends BasePage {
    public function renderDefault(string $username) {
        $profile = $this->profile();
        $this->template->username = $username;
        $this->template->comments = $profile->comments();
        $this->template->bulletpoints = $profile->bulletpoints();
        $this->template->documents = $profile->documents();
        $czechRoles = [
            'member' => 'Člen',
            'administrator' => 'Administrátor',
            'creator' => 'Tvůrce',
        ];
        $this->template->role = $czechRoles[(string)$profile->owner()->role()];
    }
    
    protected function createComponentPunishment() {
        return new Component\Punishment(
            (new Constraint\OwnedMySqlPunishments(
                $this->profile()->owner(),
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
        return new Component\Role($this->profile(), $this->identity);
    }

    protected function createComponentProfilePhoto() {
        return new Component\ProfilePhoto($this->profile()->owner());
    }

    private function profile(): User\Profile {
        try {
            (new Constraint\UsernameExistenceRule($this->database))
                ->isSatisfied($this->getParameter('username'));
            return new User\MySqlProfile(
                new Access\MySqlIdentity(
                    (new Translation\MySqlUsernameSlug(
                        $this->getParameter('username'),
                        $this->database
                    ))->origin(),
                    $this->database
                ),
                $this->database
            );
        } catch(Exception\ExistenceException $ex) {
            $this->error(
                'Uživatel neexistuje',
                IResponse::S404_NOT_FOUND
            );
        }
    }
}