<?php
namespace Bulletpoint\Component;

use Bulletpoint\Exception;
use Bulletpoint\Model\{
    User, Access
};

final class Role extends BaseControl {
    private $profile;
    private $identity;

    public function __construct(User\Profile $profile, Access\Identity $identity) {
        $this->profile = $profile;
        $this->identity = $identity;
    }

    public function render() {
        $this->template->setFile(__DIR__ . '/Role.latte');
        $this->template->identity = $this->identity;
        $this->template->owner = $this->profile->owner();
        $this->template->render();
    }

    /**
     * @secured
     */
    public function handlePovysit() {
        try {
            (new Access\RestrictedRole(
                $this->identity,
                $this->profile->owner()->role()
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
                $this->identity,
                $this->profile->owner()->role()
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