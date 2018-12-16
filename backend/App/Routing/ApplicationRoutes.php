<?php
declare(strict_types = 1);

namespace Bulletpoint\Routing;

use Bulletpoint\Domain\Access;
use Bulletpoint\Endpoint;
use Bulletpoint\Http;
use Bulletpoint\Request;
use Bulletpoint\View\AuthenticatedView;
use Klapuch\Application;
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

	public function __construct(Storage\Connection $connection, Uri $url) {
		$this->connection = $connection;
		$this->url = $url;
	}

	public function matches(): array {
		$request = new Request\CachedRequest(new Application\PlainRequest());
		$user = (new Access\PgEntrance(
			new Access\FakeEntrance(new Access\RegisteredUser(1, $this->connection)),
			$this->connection
		))->enter([]);
		return [
			'themes/{id} [GET]' => function(): Application\View {
				return new Endpoint\Theme\Get($this->connection);
			},
			'themes [POST]' => function() use ($user, $request): Application\View {
				return new AuthenticatedView(
					new Endpoint\Themes\Post($request, $this->connection, $user, $this->url),
					new Http\ChosenRole($user, ['member'])
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
			'themes/{theme_id}/bulletpoints [POST]' => function() use ($user, $request): Application\View {
				return new AuthenticatedView(
					new Endpoint\Theme\Bulletpoints\Post($request, $this->connection, $user),
					new Http\ChosenRole($user, ['member'])
				);
			},
			'bulletpoints/{id} [GET]' => function(): Application\View {
				return new Endpoint\Bulletpoint\Get($this->connection);
			},
			'bulletpoints/{bulletpoint_id}/ratings [POST]' => function() use ($request, $user): Application\View {
				return new AuthenticatedView(
					new Endpoint\Bulletpoint\Ratings\Post($request, $this->connection, $user),
					new Http\ChosenRole($user, ['member'])
				);
			},
		];
	}
}
