<?php
namespace Bulletpoint\Exception;

class UploadException extends \Exception {
    public function __construct($code, \Exception $previous = null) {
        parent::__construct($this->toMessage($code), $code, $previous);
    }

    private function toMessage($code) {
        $responses = [
            UPLOAD_ERR_INI_SIZE => 'Příliš velký soubor',
            UPLOAD_ERR_FORM_SIZE => 'Příliš velký soubor',
            UPLOAD_ERR_PARTIAL => 'Soubor byl jen částečně nahrán',
            UPLOAD_ERR_NO_FILE => 'Soubor nebyl vybrán',
            UPLOAD_ERR_NO_TMP_DIR => 'Na serveru chybí dočasná složka',
            UPLOAD_ERR_CANT_WRITE => 'Selhal zápis na disk serveru',
            UPLOAD_ERR_EXTENSION => 'Serverem nerozpoznaná koncovka'
        ];
        return $responses[$code] ?? 'Nastala chyba při nahrávání';
    }
} 