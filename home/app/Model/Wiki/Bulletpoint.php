<?php
namespace Bulletpoint\Model\Wiki;

use Bulletpoint\Model\Access;

interface Bulletpoint {
	public function author(): Access\Identity;
	public function content(): string;
	public function source(): InformationSource;
	public function id(): int;
	public function date(): \DateTime;
	public function document(): Document;
	public function edit(string $content);
}