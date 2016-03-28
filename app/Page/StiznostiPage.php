<?php
namespace Bulletpoint\Page;

use Bulletpoint\Model\{
    Report
};
use Bulletpoint\Exception;

final class StiznostiPage extends BasePage {
    public function renderDefault() {
        $this->template->complaints = (new Report\MySqlTargets(
            $this->database
        ))->iterate();
    }
}