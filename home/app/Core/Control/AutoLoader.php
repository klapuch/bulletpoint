<?php
namespace Bulletpoint\Core\Control;

final class AutoLoader {
	private $locations = [];

	public function __construct(array $locations) {
		$this->locations = $locations;
	}

	public function load(string $class) {
		if($this->isSuitableNamespace($class))
			return require $this->foundClass($class); // return statement because of avoiding to else condition
		throw new \UnexpectedValueException(
				sprintf(
				'Namespace in %s class does not have suitable name',
				$class
			)
		);
	}

	private function foundClass(string $class): string {
		foreach($this->locations as $location)
			if(is_file($this->file($class, $location)))
				return $this->file($class, $location);
		throw new \UnexpectedValueException(
			sprintf(
				'Class %s does not exist in %s folder(s)',
				$class,
				implode(' or ', $this->locations)
			)
		);
	}

	private function isSuitableNamespace(string $class): int {
		return count(array_filter(explode('\\', $class))) > 1;
	}

	private function file(string $class, string $location): string {
		$parts = explode('\\', $class);
		$parts[0] = $location; // Index 0 as owner name (Acme)
		return sprintf(
			'%s.php',
			implode('/', $parts)
		);
	}
}