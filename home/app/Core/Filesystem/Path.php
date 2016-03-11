<?php
namespace Bulletpoint\Core\Filesystem;

interface Path {
	public function folder(): string;
	public function file(): string;
	public function extension(): string;
	public function full(): string;
}