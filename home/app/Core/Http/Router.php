<?php
namespace Bulletpoint\Core\Http;

interface Router {
	public function page(): string;
	public function view(): string;
	public function parameters(): array;
}