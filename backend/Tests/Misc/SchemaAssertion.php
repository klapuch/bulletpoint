<?php
declare(strict_types = 1);

namespace Bulletpoint\Misc;

use JsonSchema;
use Tester\Assert;

final class SchemaAssertion implements Assertion {
	/** @var mixed[]|\stdClass */
	private $values;

	private \SplFileInfo $schema;

	/**
	 * @param \stdClass|array $values Copying typehint from JsonSchema\Validator::validate method
	 * @param \SplFileInfo $schema
	 */
	public function __construct($values, \SplFileInfo $schema) {
		$this->values = $values;
		$this->schema = $schema;
	}

	public function assert(): void {
		if (is_array($this->values)) {
			foreach ($this->values as $value) {
				(new self($value, $this->schema))->assert();
			}
		} else {
			$validator = new JsonSchema\Validator();
			$validator->validate(
				$this->values,
				['$ref' => 'file://' . $this->schema->getRealPath()],
			);
			$error = $validator->getErrors();
			Assert::true(
				$validator->isValid(),
				isset($error['message'], $error['property']) === false ? null : sprintf('%s: %s', $error['message'], $error['property']),
			);
		}
	}
}
