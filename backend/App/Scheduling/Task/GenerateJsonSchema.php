<?php
declare(strict_types = 1);

namespace Bulletpoint\Scheduling\Task;

use Bulletpoint\Schema;
use Klapuch\Http;
use Klapuch\Scheduling;
use Klapuch\Storage;
use Klapuch\Uri;
use Nette\Utils\Json;

final class GenerateJsonSchema implements Scheduling\Job {
	/** @var \Klapuch\Storage\Connection */
	private $connection;

	public function __construct(Storage\Connection $connection) {
		$this->connection = $connection;
	}

	public function fulfill(): void {
		$schemas = new class {
			/**
			 * @param string $schema
			 * @throws \UnexpectedValueException
			 */
			private function validate(string $schema): void {
				$response = (new Http\BasicRequest(
					'POST',
					new Uri\ValidUrl('https://www.jsonschemavalidator.net/api/jsonschema/validate'),
					[
						CURLOPT_HTTPHEADER => [
							'Content-Type: application/json',
							'X-Csrf-Token: LsAV3irUxESTZz-djmy6u5czf122eyTgu3yvdi6MSOwQANDhsOHOQzBZrqPku09Z8KS8BIE406uNXXeAaSycv978wm81:EYgPsfAI3loDTk9UhNmva8lcEE5KwhHSUbD_zTktXHmaO7iA36crJ8eAB0rum1vjF3VeIaKiC4GIPRTtJG8ydDuUdt41',
						],
					],
					Json::encode(['json' => '', 'schema' => $schema]),
				))->send();
				$validation = Json::decode($response->body(), Json::FORCE_ARRAY);
				if (!$validation['valid']) {
					throw new \Exception('JSON schema is not valid');
				}
			}

			public function save(array $json, \SplFileInfo $file): void {
				@mkdir($file->getPath(), 0777, true); // @ directory may exists
				$schema = Json::encode($json, Json::PRETTY);
				try {
					$this->validate($schema);
				} catch (\UnexpectedValueException $e) {
					throw new \Exception(sprintf('JSON schema %s is not valid', $file->getPathname()), 0, $e);
				}
				file_put_contents($file->getPathname(), $schema);
			}
		};

		$this->withoutRemains();

		$demand = new Schema\Bulletpoint\Rating\Structure($this->connection);
		$schemas->save($demand->post(), new \SplFileInfo(__DIR__ . '/../../Endpoint/Bulletpoint/Ratings/schema/post.json'));

		$token = new Schema\Token\Structure();
		$schemas->save($token->post(), new \SplFileInfo(__DIR__ . '/../../Endpoint/Tokens/schema/post.json'));

		$facebookToken = new Schema\FacebookToken\Structure();
		$schemas->save($facebookToken->post(), new \SplFileInfo(__DIR__ . '/../../Endpoint/Tokens/Facebook/schema/post.json'));

		$refreshToken = new Schema\RefreshToken\Structure();
		$schemas->save($refreshToken->post(), new \SplFileInfo(__DIR__ . '/../../Endpoint/RefreshTokens/schema/post.json'));

		$theme = new Schema\Theme\Structure($this->connection);
		$schemas->save($theme->post(), new \SplFileInfo(__DIR__ . '/../../Endpoint/Themes/schema/post.json'));
		$schemas->save($theme->put(), new \SplFileInfo(__DIR__ . '/../../Endpoint/Theme/schema/put.json'));
		$schemas->save($theme->patch(), new \SplFileInfo(__DIR__ . '/../../Endpoint/Theme/schema/patch.json'));
		$schemas->save($theme->get(), new \SplFileInfo(__DIR__ . '/../../Endpoint/Themes/schema/get.json'));
		$schemas->save($theme->get(), new \SplFileInfo(__DIR__ . '/../../Endpoint/Theme/schema/get.json'));

		$themeBulletpoint = new Schema\Theme\Bulletpoint\Structure($this->connection);
		$schemas->save($themeBulletpoint->post(), new \SplFileInfo(__DIR__ . '/../../Endpoint/Theme/Bulletpoints/schema/post.json'));
		$schemas->save($themeBulletpoint->get(), new \SplFileInfo(__DIR__ . '/../../Endpoint/Bulletpoint/schema/get.json'));
		$schemas->save($themeBulletpoint->put(), new \SplFileInfo(__DIR__ . '/../../Endpoint/Bulletpoint/schema/put.json'));
		$schemas->save($themeBulletpoint->get(), new \SplFileInfo(__DIR__ . '/../../Endpoint/Theme/Bulletpoints/schema/get.json'));
		$schemas->save($themeBulletpoint->patch(), new \SplFileInfo(__DIR__ . '/../../Endpoint/Bulletpoint/schema/patch.json'));

		$themeContributedBulletpoint = new Schema\Theme\ContributedBulletpoint\Structure($this->connection);
		$schemas->save($themeContributedBulletpoint->post(), new \SplFileInfo(__DIR__ . '/../../Endpoint/Theme/ContributedBulletpoints/schema/post.json'));
		$schemas->save($themeContributedBulletpoint->get(), new \SplFileInfo(__DIR__ . '/../../Endpoint/ContributedBulletpoint/schema/get.json'));
		$schemas->save($themeContributedBulletpoint->put(), new \SplFileInfo(__DIR__ . '/../../Endpoint/ContributedBulletpoint/schema/put.json'));
		$schemas->save($themeContributedBulletpoint->get(), new \SplFileInfo(__DIR__ . '/../../Endpoint/Theme/ContributedBulletpoints/schema/get.json'));

		$tags = new Schema\Tag\Structure();
		$schemas->save($tags->post(), new \SplFileInfo(__DIR__ . '/../../Endpoint/Tags/schema/post.json'));
		$schemas->save($tags->get(), new \SplFileInfo(__DIR__ . '/../../Endpoint/Tags/schema/get.json'));
	}

	public function name(): string {
		return 'GenerateJsonSchema';
	}

	private function withoutRemains(): void {
		foreach (new \CallbackFilterIterator(
			new \RecursiveIteratorIterator(
				new \RecursiveDirectoryIterator(__DIR__ . '/../../Endpoint', \RecursiveDirectoryIterator::SKIP_DOTS),
				\RecursiveIteratorIterator::SELF_FIRST,
				\RecursiveIteratorIterator::CATCH_GET_CHILD,
			),
			static function (\SplFileInfo $file): bool {
				return $file->isDir() && (
					file_exists(sprintf('%s/get.json', $file->getPathname()))
					|| file_exists(sprintf('%s/post.json', $file->getPathname()))
					|| file_exists(sprintf('%s/put.json', $file->getPathname()))
					|| file_exists(sprintf('%s/patch.json', $file->getPathname()))
				);
			},
		) as $directory) {
			/** @var \SplFileInfo $directory */
			array_map('unlink', glob(sprintf('%s/*.json', $directory->getPathname())));
			if (!rmdir($directory->getPathName())) {
				throw new \RuntimeException(sprintf('%s was not removed', $directory->getPathname()));
			}
		}
	}
}
