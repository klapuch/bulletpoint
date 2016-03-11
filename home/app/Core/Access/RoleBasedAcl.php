<?php
namespace Bulletpoint\Core\Access;

use Bulletpoint\Core\Control;

final class RoleBasedAcl implements Acl {
	private $configuration;
	private $role;

	public function __construct(
		Control\Configuration $configuration,
		string $role
	) {
		$this->configuration = $configuration;
		$this->role = $role;
	}

	public function list(): array {
		return array_values(
			array_unique(
				array_filter(
					array_map(
						'trim',
						explode(
							',',
							$this->configuration->toSection('acl')->{$this->role}
						)
					)
				)
			)
		);
	}
}