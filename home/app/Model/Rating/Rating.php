<?php
namespace Bulletpoint\Model\Rating;

interface Rating {
	const PROS = '+1';
	const CONS = '-1';
	public function increment();
	public function decrement();
	public function pros(): int;
	public function cons(): int;
}