<?php
namespace Bulletpoint\Model\Text;

use Texy;

abstract class Format {
    protected $texy;

    public function __construct(Texy\Texy $texy) {
        $this->texy = $texy;
    }

    protected function basicSetting() {
        $this->texy->encoding = 'UTF-8';
        $this->texy->allowedClasses = false;
        $this->texy->allowedStyles = false;
    }

    protected function loadModules() {
        $this->texy->allowed['phrase/em'] = true;
        $this->texy->allowed['phrase/code'] = false;
        $this->texy->allowed['script'] = false;
        $this->texy->allowed['html/comment'] = false;
        $this->texy->allowed['html/tag'] = true;
        $this->texy->allowed['blocks'] = false;
    }

    private function setting() {
        $this->basicSetting();
        $this->loadModules();
    }

    public function process($text) {
        $this->setting();
        return str_replace(
            ["\xc2\xa0", "\xc2\xad", "\xe2\x80\x93", "\xe2\x80\x94"],
            ['&nbsp;', '&shy;', '&ndash;', '&mdash;'],
            $this->texy->process($text)
        );
    }
}