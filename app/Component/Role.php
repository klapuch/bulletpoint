<?php
namespace Bulletpoint\Component;

use Bulletpoint\Exception;
use Bulletpoint\Model\{
    User, Access
};

final class Role extends BaseControl {
    const CZECH_ROLES = [
        'member' => 'Člen',
        'administrator' => 'Administrátor',
        'creator' => 'Tvůrce',
    ];
    private $owner;
    private $myself;

    public function __construct(
        Access\Identity $owner,
        Access\Identity $myself
    ) {
        parent::__construct();
        $this->owner = $owner;
        $this->myself = $myself;
    }

    public function render() {
        $this->template->setFile(__DIR__ . '/Role.latte');
        $this->template->myself = $this->myself;
        $this->template->owner = $this->owner;
        $this->template->czechRole = self::CZECH_ROLES[
            (string)$this->owner->role()
        ];
        $this->template->render();
    }

    /**
     * @secured
     */
    public function handlePovysit() {
        try {
            (new Access\RestrictedRole(
                $this->myself,
                $this->owner->role()
            ))->promote();
            $this->presenter->flashMessage('Uživatel je povýšen', 'success');
        } catch(\OverflowException $ex) {
            $this->presenter->flashMessage($ex->getMessage(), 'danger');
        } catch(Exception\AccessDeniedException $ex) {
            $this->presenter->flashMessage($ex->getMessage(), 'danger');
        } catch(Exception\StorageException $ex) {
            $this->presenter->flashMessage($ex->getMessage(), 'danger');
        } finally {
            $this->presenter->redirect('this');
        }
    }

    /**
     * @secured
     */
    public function handleDegradovat() {
        try {
            (new Access\RestrictedRole(
                $this->myself,
                $this->owner->role()
            ))->degrade();
            $this->presenter->flashMessage('Uživatel je degradován', 'success');
        } catch(\UnderflowException $ex) {
            $this->presenter->flashMessage($ex->getMessage(), 'danger');
        } catch(Exception\AccessDeniedException $ex) {
            $this->presenter->flashMessage($ex->getMessage(), 'danger');
        } catch(Exception\StorageException $ex) {
            $this->presenter->flashMessage($ex->getMessage(), 'danger');
        } finally {
            $this->presenter->redirect('this');
        }
    }
}