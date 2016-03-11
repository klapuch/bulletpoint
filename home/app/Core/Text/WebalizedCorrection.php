<?php
namespace Bulletpoint\Core\Text;

final class WebalizedCorrection implements Correction {
    public function replacement($origin) {
        setlocale(LC_CTYPE, 'cs_CZ');
        return $this->withoutTrailCharacters(
            $this->lowerCase(
                $this->translited(
                    $this->withTrimmedDashes(
                        $this->toDashes($origin)
                    )
                )
            )
        );
    }

    private function withoutTrailCharacters($origin) {
        return preg_replace('~[^-a-z0-9_]+~', '', $origin);
    }

    private function lowerCase($origin) {
        return mb_strtolower($origin);
    }

    private function translited($origin) {
        return iconv('utf-8', 'us-ascii//TRANSLIT', $origin);
    }

    private function withTrimmedDashes($origin) {
        return trim($origin, '-');
    }

    private function toDashes($origin) {
        return preg_replace('~[^\\pL0-9_]+~u', '-', $origin);
    }
}