<?php
namespace Bulletpoint\Component;

use Bulletpoint\Model\{
    Access, Filesystem, Paths, User
};

final class ProfilePhoto extends BaseControl {
    private $owner;

    public function __construct(Access\Identity $owner) {
        parent::__construct();
        $this->owner = $owner;
    }

    public function createTemplate() {
        $template = parent::createTemplate();
        $template->setFile(__DIR__ . '/ProfilePhoto.latte');
        $template->class = '';
        $template->username = $this->owner->username();
        $template->photo = $this->photo();
        return $template;
    }

    public function render() {
        $this->template->render();
    }

    public function renderCenter() {
        $this->template->class = 'center-block';
        $this->template->render();
    }

    private function photo() {
        return (new User\ProfilePhoto(
            $this->owner,
            new Filesystem\Folder(Paths::profileImage())
        ))->show()->asFile()->location();
    }
}