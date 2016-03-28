<?php
namespace Bulletpoint\Model\Text;

final class PublishingFormat extends Format {
    protected function loadModules() {
        parent::loadModules();
        $this->texy->allowed['heading/surrounded'] = false;
        $this->texy->allowed['heading/underlined'] = false;
        $this->texy->allowed['horizline'] = false;
        $this->texy->allowed['phrase/em-alt'] = true;
        $this->texy->allowed['table'] = false;
        $this->texy->allowed['list/definition'] = false;
        $this->texy->allowed['image'] = false;
        $this->texy->allowed['blockquote'] = false;
        $this->texy->allowed['list'] = false;
        $this->texy->allowed['html/tag'] = false;
        $this->texy->alignClasses['center'] = 'text-center';
        $this->texy->headingModule->top = 5;
        $this->texy->linkModule->forceNoFollow = true;
    }
}