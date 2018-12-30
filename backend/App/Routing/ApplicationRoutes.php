<?php
declare(strict_types = 1);

namespace Bulletpoint\Routing;

use Bulletpoint\Domain\Access;
use Bulletpoint\Endpoint;
use Bulletpoint\Http;
use Bulletpoint\Misc;
use Bulletpoint\Request\CachedRequest;
use Bulletpoint\View\AuthenticatedView;
use Klapuch\Application;
use Klapuch\Encryption;
use Klapuch\Routing;
use Klapuch\Storage;
use Klapuch\Uri\Uri;

/**
 * Routes for whole application
 */
final class ApplicationRoutes implements Routing\Routes {
	/** @var \Klapuch\Storage\Connection */
	private $connection;

	/** @var \Klapuch\Uri\Uri */
	private $url;

	/** @var \Klapuch\Encryption\Cipher */
	private $cipher;

	public function __construct(Storage\Connection $connection, Uri $url, Encryption\Cipher $cipher) {
		$this->connection = $connection;
		$this->url = $url;
		$this->cipher = $cipher;
	}

	public function matches(): array {
		$request = new CachedRequest(new Application\PlainRequest());
		$user = (new Access\HarnessedEntrance(
			new Access\PgEntrance(
				new Access\ApiEntrance($this->connection),
				$this->connection
			),
			new Misc\ApiErrorCallback(HTTP_TOO_MANY_REQUESTS)
		))->enter($request->headers());
		return [
			'themes/{id} [GET]' => function(): Application\View {
				return new Endpoint\Theme\Get($this->connection);
			},
			'themes/{id} [PUT]' => function() use ($user, $request): Application\View {
				return new AuthenticatedView(
					new Endpoint\Theme\Put($request, $this->connection),
					new Http\ChosenRole($user, ['admin'])
				);
			},
			'themes [POST]' => function() use ($user, $request): Application\View {
				return new AuthenticatedView(
					new Endpoint\Themes\Post($request, $this->connection, $user, $this->url),
					new Http\ChosenRole($user, ['admin'])
				);
			},
			'themes [GET]' => function(): Application\View {
				return new Endpoint\Themes\Get($this->connection);
			},
			'tags [GET]' => function(): Application\View {
				return new Endpoint\Tags\Get($this->connection);
			},
			'themes/{theme_id}/bulletpoints [GET]' => function(): Application\View {
				return new Endpoint\Theme\Bulletpoints\Get($this->connection);
			},
			'themes/{theme_id}/contributed_bulletpoints [GET]' => function() use ($user): Application\View {
				return new Endpoint\Theme\ContributedBulletpoints\Get($this->connection, $user);
			},
			'themes/{theme_id}/bulletpoints [POST]' => function() use ($user, $request): Application\View {
				return new AuthenticatedView(
					new Endpoint\Theme\Bulletpoints\Post($request, $this->connection, $user),
					new Http\ChosenRole($user, ['admin'])
				);
			},
			'themes/{theme_id}/contributed_bulletpoints [POST]' => function() use ($user, $request): Application\View {
				return new AuthenticatedView(
					new Endpoint\Theme\ContributedBulletpoints\Post($request, $this->connection, $user),
					new Http\ChosenRole($user, ['member', 'admin'])
				);
			},
			'bulletpoints/{id} [GET]' => function(): Application\View {
				return new Endpoint\Bulletpoint\Get($this->connection);
			},
			'bulletpoints/{id} [DELETE]' => function(): Application\View {
				return new Endpoint\Bulletpoint\Delete($this->connection);
			},
			'bulletpoints/{id} [PUT]' => function() use ($request, $user): Application\View {
				return new AuthenticatedView(
					new Endpoint\Bulletpoint\Put($request, $this->connection),
					new Http\ChosenRole($user, ['admin'])
				);
			},
			'contributed_bulletpoints/{id} [GET]' => function() use ($user): Application\View {
				return new Endpoint\ContributedBulletpoint\Get($this->connection, $user);
			},
			'contributed_bulletpoints/{id} [DELETE]' => function() use ($user): Application\View {
				return new Endpoint\ContributedBulletpoint\Delete($this->connection, $user);
			},
			'contributed_bulletpoints/{id} [PUT]' => function() use ($request, $user): Application\View {
				return new AuthenticatedView(
					new Endpoint\ContributedBulletpoint\Put($request, $this->connection, $user),
					new Http\ChosenRole($user, ['member'])
				);
			},
			'bulletpoints/{bulletpoint_id}/ratings [POST]' => function() use ($request, $user): Application\View {
				return new AuthenticatedView(
					new Endpoint\Bulletpoint\Ratings\Post($request, $this->connection, $user),
					new Http\ChosenRole($user, ['member', 'admin'])
				);
			},
			'tokens [POST]' => function() use ($request): Application\View {
				return new Endpoint\Tokens\Post(
					$request,
					$this->connection,
					$this->cipher
				);
			},
			'tokens [DELETE]' => static function() use ($user): Application\View {
				return new AuthenticatedView(
					new Endpoint\Tokens\Delete(),
					new Http\ChosenRole($user, ['member', 'admin'])
				);
			},
			'refresh_tokens [POST]' => static function() use ($request): Application\View {
				return new Endpoint\RefreshTokens\Post($request);
			},
			'users/me [GET]' => function() use ($user): Application\View {
				return new AuthenticatedView(
					new Endpoint\Users\Me\Get($this->connection, $user),
					new Http\ChosenRole($user, ['member', 'admin'])
				);
			},
		];
	}
}
