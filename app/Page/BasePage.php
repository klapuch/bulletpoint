<?php
namespace Bulletpoint\Page;

use Nette;
use Nette\Http\IResponse;
use Nette\Security;
use Bulletpoint\Model\Access;
use Bulletpoint\Model\Constraint;

abstract class BasePage extends Nette\Application\UI\Presenter {
    use \Nextras\Application\UI\SecuredLinksPresenterTrait;

    /** @inject @var \Bulletpoint\Model\Storage\PDODatabase */
    public $database;
    /** @inject @var \Bulletpoint\Model\Access\Identity */
    public $identity;
    /** @inject @var \Bulletpoint\Model\Text\PublishingFormat */
    public $texy;

    public function checkRequirements($element) {
        if($this->signal === null) {
            $resource = $this->name;
            $action = $this->action;
        } elseif($this->signal && empty($this->signal[0])) {
            $resource = $this->name;
            $action = $this->signal[1] . '!';
        } elseif($this->signal && $this->signal[0]) {
            $resource = preg_replace('~-[0-9]+$~', '', $this->signal[0]);
            $action = $this->signal[1] . '!';
        }
        if(!$this->user->isAllowed($resource, $action)) {
            if($this->user->loggedIn) {
                $this->error(
                    'Na tuto stránku nemáte dostatečné oprávnění',
                    IResponse::S403_FORBIDDEN
                );
            }
            $this->flashMessage('Je třeba se nejdřív přihlásit', 'danger');
            $this->redirect(
                'Prihlasit:',
                ['backlink' => $this->storeRequest()]
            );
        }
    }

    public function startup() {
        if(!$this->user->loggedIn) {
            if($this->user->logoutReason=== Security\IUserStorage::INACTIVITY) {
                $this->flashMessage(
                    'Byl jsi odhlášen z důvodu neaktivity',
                    'danger'
                );
                $this->redirect(
                    'Prihlasit:',
                    ['backlink' => $this->storeRequest()]
                );
            }
        } else
            $this->isPunished($this->identity);
        parent::startup();
    }

    protected function createTemplate() {
        $template = parent::createTemplate();
        $template->registerHelper('texy', [$this->texy, 'process']);
        return $template;
    }

    public function afterRender() {
        if($this->isAjax() && $this->hasFlashSession())
            $this->redrawControl('flashes');
    }

    protected function isPunished(Access\Identity $identity): bool {
        $punishment = (new Constraint\OwnedMySqlPunishments(
            $identity,
            $this->database,
            new Constraint\ActualMySqlPunishments(
                $identity,
                $this->database
            )
        ))->iterate()->current();
        if(!$punishment->expired()) {
            $this->user->logout(true);
            $this->flashMessage(
                sprintf(
                    'Tvůj učet je zablokován do **%s** z důvodu **%s**',
                    $punishment->expiration()->format('j.n.Y H:i'),
                    $punishment->reason()
                ),
                'danger'
            );
            $this->redirect('Prihlasit:');
            return true;
        }
        return false;
    }
}
