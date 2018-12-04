<?php
declare(strict_types = 1);

namespace Bulletpoint\Scheduling;

final class SelectedJob implements Job {
	/** @var string */
	private $name;

	/** @var \Bulletpoint\Scheduling\Job[] */
	private $origins;

	public function __construct(string $name, Job ...$origins) {
		$this->name = $name;
		$this->origins = $origins;
	}

	/**
	 * @throws \UnexpectedValueException
	 */
	public function fulfill(): void {
		/** @var \Bulletpoint\Scheduling\Job[] $choices */
		$choices = array_combine(
			array_map(
				static function(Job $origin): string {
					return $origin->name();
				},
				$this->origins
			),
			$this->origins
		);
		if (!isset($choices[$this->name]))
			throw new \UnexpectedValueException(sprintf('Job "%s" does not exist', $this->name));
		$choices[$this->name]->fulfill();
	}

	public function name(): string {
		return '';
	}
}
