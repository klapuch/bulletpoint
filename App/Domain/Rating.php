<?php declare(strict_types = 1);

namespace Bulletpoint\Domain;

interface Rating {
	public function up(): void;
	public function down(): void;
	public function reset(): void;
}