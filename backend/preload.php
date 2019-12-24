<?php
declare(strict_types = 1);

foreach (array_merge(require __DIR__ . '/vendor/composer/autoload_classmap.php', require __DIR__ . '/vendor/composer/autoload_files.php') as $filename) {
	opcache_compile_file($filename);
}
