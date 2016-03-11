<?php
namespace Bulletpoint\Core\Text;

interface Correction {
	public function replacement($origin);
}