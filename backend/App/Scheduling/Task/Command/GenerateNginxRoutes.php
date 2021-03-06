<?php
declare(strict_types = 1);

namespace Bulletpoint\Scheduling\Task\Command;

use Klapuch\Configuration;
use Klapuch\Scheduling;

final class GenerateNginxRoutes implements Scheduling\Job {
	private Configuration\Source $source;

	private \SplFileInfo $destination;

	public function __construct(Configuration\Source $source, \SplFileInfo $destination) {
		$this->source = $source;
		$this->destination = $destination;
	}

	/**
	 * @throws \UnexpectedValueException
	 */
	public function fulfill(): void {
		file_put_contents(
			$this->destination->getPathname(),
			$this->locations($this->source->read()) . "\n",
		);
	}

	private function locations(array $source): string {
		return '# Automatically generated, do not manually edit' . PHP_EOL . implode(
			PHP_EOL . PHP_EOL,
			array_map(
				function(string $name, array $block): string {
					$directives = implode(
						PHP_EOL,
						array_filter(
							[
								sprintf('fastcgi_param ROUTE_NAME "%s";', $name),
								$this->params($block['params'] ?? []),
								$this->types($block['types'] ?? []),
								'	include snippets/php.conf;',
								$this->limitExcept($block['methods']),
								$this->preflight($block['methods']),
								$this->lines($block['line'] ?? []),
							],
						),
					);
					return <<<CONF
					{$this->location($block['params'] ?? [], $block['location'])} {
						{$directives}
					}
					CONF;
				},
				array_keys($source),
				$source,
			),
		);
	}

	private function lines(array $lines): string {
		if ($lines === [])
			return '';
		return "\t" . implode("\n\t", $lines);
	}

	private function preflight(array $methods): string {
		if (in_array('OPTIONS', $methods, true))
			return '';
		return '	include snippets/preflight.conf;';
	}

	private function limitExcept(array $methods): string {
		$except = implode(' ', array_unique([...$methods, 'OPTIONS']));
		return <<<CONF
			limit_except {$except} {
				deny all;
			}
		CONF;
	}

	private function params(array $params): string {
		if ($params === [])
			return '';
		$query = implode(
			'&',
			array_map(
				static fn(string $param): string => sprintf('%1$s=$%1$s', $param),
				array_keys($params),
			),
		);
		return <<<CONF
			fastcgi_param ROUTE_PARAM_QUERY {$query};
		CONF;
	}

	private function types(array $types): string {
		if ($types === [])
			return '';
		$query = implode(
			'&',
			array_map(
				static fn(string $param, string $type): string => sprintf('%s=%s', $type, $param),
				$types,
				array_keys($types),
			),
		);
		return <<<CONF
			fastcgi_param ROUTE_TYPE_QUERY {$query};
		CONF;
	}

	private function location(array $params, string $sample): string {
		return sprintf(
			'location %s',
			str_replace(
				array_map(
					static fn(string $name): string => sprintf('{%s}', $name),
					array_keys($params),
				),
				array_map(
					static fn(string $name, string $regex): string => sprintf('(?<%s>%s)', $name, $regex),
					array_keys($params),
					$params,
				),
				$sample,
			),
		);
	}

	public function name(): string {
		return 'GenerateNginxRoutes';
	}
}
