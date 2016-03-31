<?php
namespace Bulletpoint\Page;

use Bulletpoint\Model\{
    Report
};

final class StiznostiPage extends BasePage {
    public function renderDefault() {
        $this->template->targets = (new Report\MySqlTargets(
            $this->database
        ))->iterate();
    }
}