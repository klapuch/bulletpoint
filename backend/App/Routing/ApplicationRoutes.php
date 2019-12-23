<?php
declare(strict_types = 1);

namespace Bulletpoint\Routing;

use Bulletpoint\Api\Endpoint;
use Bulletpoint\Domain\Access;
use Bulletpoint\Http;
use Bulletpoint\View\AuthenticatedView;
use Klapuch\Application;
use Klapuch\Encryption;
use Klapuch\Routing;
use Klapuch\Storage;
use Klapuch\Uri\Uri;
use Sentry;

/**
 * Routes for whole application
 */
final class ApplicationRoutes implements Routing\Routes {
	private Storage\Connection $connection;
	private Uri $url;
	private Encryption\Cipher $cipher;

	public function __construct(Storage\Connection $connection, Uri $url, Encryption\Cipher $cipher) {
		$this->connection = $connection;
		$this->url = $url;
		$this->cipher = $cipher;
	}

	public function matches(): array {
		$request = new Application\CachedRequest(new Application\PlainRequest());
		$user = (new Access\PgEntrance(
			new Access\ApiEntrance($this->connection),
			$this->connection,
		))->enter($request->headers());
		Sentry\configureScope(static function (Sentry\State\Scope $scope) use ($user): void {
			$scope->setUser($user->properties());
		});
		return [
			'avatars [POST]' => fn(): Application\View => new AuthenticatedView(
				new Endpoint\Avatars\Post($this->connection, $user),
				new Http\ChosenRole($user, ['admin', 'member']),
			),
			'themes/{id} [GET]' => fn(): Application\View => new Endpoint\Theme\Get($this->connection),
			'themes/{id} [PUT]' => fn(): Application\View => new AuthenticatedView(
				new Endpoint\Theme\Put($request, $this->connection),
				new Http\ChosenRole($user, ['admin']),
			),
			'themes/{id} [PATCH]' => fn(): Application\View => new AuthenticatedView(
				new Endpoint\Theme\Patch($request, $this->connection, $user),
				new Http\ChosenRole($user, ['admin', 'member']),
			),
			'themes [POST]' => fn(): Application\View => new AuthenticatedView(
				new Endpoint\Themes\Post($request, $this->connection, $user, $this->url),
				new Http\ChosenRole($user, ['admin']),
			),
			'themes [GET]' => fn(): Application\View => new Endpoint\Themes\Get($this->connection, $this->url),
			'tags [GET]' => fn(): Application\View => new Endpoint\Tags\Get($this->connection),
			'starred_tags [GET]' => fn(): Application\View => new AuthenticatedView(
				new Endpoint\StarredTags\Get($this->connection, $user),
				new Http\ChosenRole($user, ['member', 'admin']),
			),
			'tags [POST]' => fn(): Application\View => new Endpoint\Tags\Post($this->connection, $request),
			'themes/{theme_id}/bulletpoints [GET]' => fn(): Application\View => new Endpoint\Theme\Bulletpoints\Get($this->connection),
			'themes/{theme_id}/contributed_bulletpoints [GET]' => fn(): Application\View => new Endpoint\Theme\ContributedBulletpoints\Get($this->connection, $user),
			'themes/{theme_id}/bulletpoints [POST]' => fn(): Application\View => new AuthenticatedView(
				new Endpoint\Theme\Bulletpoints\Post($request, $this->connection, $user),
				new Http\ChosenRole($user, ['admin']),
			),
			'themes/{theme_id}/contributed_bulletpoints [POST]' => fn(): Application\View => new AuthenticatedView(
				new Endpoint\Theme\ContributedBulletpoints\Post($request, $this->connection, $user),
				new Http\ChosenRole($user, ['member', 'admin']),
			),
			'bulletpoints/{id} [GET]' => fn(): Application\View => new Endpoint\Bulletpoint\Get($this->connection),
			'bulletpoints/{id} [DELETE]' => fn(): Application\View => new AuthenticatedView(
				new Endpoint\Bulletpoint\Delete($this->connection),
				new Http\ChosenRole($user, ['admin']),
			),
			'bulletpoints/{id} [PUT]' => fn(): Application\View => new AuthenticatedView(
				new Endpoint\Bulletpoint\Put($request, $this->connection),
				new Http\ChosenRole($user, ['admin']),
			),
			'bulletpoints/{id} [PATCH]' => fn(): Application\View => new AuthenticatedView(
				new Endpoint\Bulletpoint\Patch($request, $this->connection, $user),
				new Http\ChosenRole($user, ['admin', 'member']),
			),
			'contributed_bulletpoints/{id} [GET]' => fn(): Application\View => new Endpoint\ContributedBulletpoint\Get($this->connection, $user),
			'contributed_bulletpoints/{id} [DELETE]' => fn(): Application\View => new AuthenticatedView(
				new Endpoint\ContributedBulletpoint\Delete($this->connection, $user),
				new Http\ChosenRole($user, ['admin', 'member']),
			),
			'contributed_bulletpoints/{id} [PUT]' => fn(): Application\View => new AuthenticatedView(
				new Endpoint\ContributedBulletpoint\Put($request, $this->connection, $user),
				new Http\ChosenRole($user, ['member']),
			),
			'tokens [POST]' => fn(): Application\View => new Endpoint\Tokens\Post(
				$request,
				$this->connection,
				$this->cipher,
			),
			'tokens [DELETE]' => static fn(): Application\View => new AuthenticatedView(
				new Endpoint\Tokens\Delete(),
				new Http\ChosenRole($user, ['member', 'admin']),
			),
			'refresh_tokens [POST]' => static fn(): Application\View => new Endpoint\RefreshTokens\Post($request),
			'users/me [GET]' => fn(): Application\View => new AuthenticatedView(
				new Endpoint\Users\Me\Get($this->connection, $user),
				new Http\ChosenRole($user, ['member', 'admin']),
			),
			'users/me [PUT]' => fn(): Application\View => new AuthenticatedView(
				new Endpoint\Users\Me\Put($request, $this->connection, $user),
				new Http\ChosenRole($user, ['member', 'admin']),
			),
			'users/{id} [GET]' => fn(): Application\View => new Endpoint\User\Get($this->connection),
			'users/{id}/tags [GET]' => fn(): Application\View => new Endpoint\User\Tags\Get($this->connection),
		];
	}
}
