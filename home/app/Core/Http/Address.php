<?php
namespace Bulletpoint\Core\Http;

interface Address {
	public function pathname(): array;
	public function basename(): string;
}