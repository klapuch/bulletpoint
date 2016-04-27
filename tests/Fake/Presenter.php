<?php
namespace Bulletpoint\Fake;

use Testbench;
use Nette\Application\UI;

final class Presenter extends Testbench\PresenterMock {
    public function createSecuredLink(
        UI\PresenterComponent $component,
        $link,
        $destination
    ) {
        return $destination;
    }
}