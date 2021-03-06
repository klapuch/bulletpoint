<?php
declare(strict_types = 1);

namespace Bulletpoint\Http;

use Klapuch\Uri\Uri;

/**
 * Resource used for identification in Location header after successful POST request with 201 code
 */
final class CreatedResourceUrl implements Uri {
	private const DELIMITER = '/';

	private Uri $origin;

	/** @var mixed[] */
	private array $parameters;

	public function __construct(Uri $origin, array $parameters) {
		$this->origin = $origin;
		$this->parameters = $parameters;
	}

	/**
	 * @throws \UnexpectedValueException
	 */
	public function path(): string {
		$parts = explode(self::DELIMITER, $this->origin->path());
		$replacements = $this->replacements(
			$this->placeholders($parts),
			$this->parameters,
		) + $parts;
		ksort($replacements);
		return ltrim(implode(self::DELIMITER, $replacements), self::DELIMITER);
	}

	/**
	 * @throws \UnexpectedValueException
	 */
	public function reference(): string {
		return rtrim(
			$this->origin->reference(),
			self::DELIMITER,
		) . self::DELIMITER . $this->path();
	}

	public function query(): array {
		return $this->origin->query();
	}

	/**
	 * Placeholders extracted from parts
	 * @param array $parts
	 * @return array
	 */
	private function placeholders(array $parts): array {
		return array_map(
			static fn(string $placeholder): string => str_replace(['{', '}'], '', $placeholder),
			preg_grep('~^{.+}$~', $parts),
		);
	}

	/**
	 * Placeholders replaced by parameters
	 * @param array $placeholders
	 * @param array $parameters
	 * @throws \UnexpectedValueException
	 * @return array
	 */
	private function replacements(array $placeholders, array $parameters): array {
		$lost = $this->lost($placeholders, $parameters);
		if ($lost !== [])
			throw new \UnexpectedValueException($this->format($lost));
		return array_map(
			static fn(string $placeholder): string => (string) $parameters[$placeholder],
			$placeholders,
		);
	}

	/**
	 * Placeholders without counterpart in parameters
	 * @param array $placeholders
	 * @param array $parameters
	 * @return array
	 */
	private function lost(array $placeholders, array $parameters): array {
		return array_filter(
			$placeholders,
			static fn(string $placeholder): bool => !isset($parameters[$placeholder]),
		);
	}

	/**
	 * Format message with the lost parameters/placeholders
	 * @param array $lost
	 * @return string
	 */
	private function format(array $lost): string {
		$plural = count($lost) > 1;
		return sprintf(
			'Placeholder%s "%s" %s unused',
			$plural ? 's' : '',
			implode(', ', $lost),
			$plural ? 'are' : 'is',
		);
	}
}
