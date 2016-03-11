<?php
namespace Bulletpoint\Model\Wiki;

use Bulletpoint\Model\Access;

interface BulletpointProposal {
	public function author(): Access\Identity;
	public function content(): string;
	public function source(): InformationSource;
	public function id(): int;
	public function date(): \Datetime;
	public function document(): Document;
	public function edit(string $content);
	public function accept(): Bulletpoint;
	public function reject(string $reason = null);
}