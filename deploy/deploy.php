<?php
declare(strict_types = 1);

namespace Deployer;

require 'recipe/common.php';

set('application', 'bulletpoint');
set('repository', 'git@github.com:klapuch/bulletpoint.git');
set('git_tty', true);

set('allow_anonymous_stats', false);

set('shared_dirs', ['backend/logs', 'backend/data']);

host('95.168.218.174')
	->user('bulletpoint')
	->set('deploy_path', '/var/www/bulletpoint');

task('composer:install', static function (): void {
	cd('{{release_path}}/backend');
	run('composer install --no-dev --no-interaction --prefer-dist --no-scripts --no-progress --no-suggest --classmap-authoritative');
});

task('services:reload', static function (): void {
	run('sudo /usr/sbin/service nginx reload');
	run('sudo /usr/sbin/service php7.3-fpm reload');
});

task('cache:clear', static function (): void {
	run('redis-cli -p 6379 flushall');
});

task('passwords:put', static function (): void {
	run(sprintf("sed -i -e 's/\${ENV_PGPASSWORD}/%s/g' {{release_path}}/backend/App/Configuration/secrets.sample.ini", getenv('ENV_PGPASSWORD')));
	run(sprintf('cp {{release_path}}/backend/App/Configuration/secrets.sample.ini {{release_path}}/backend/App/Configuration/secrets.ini'));
});

task('migrations:run', static function (): void {
	cd('{{release_path}}/backend');
	run('make migration-run-new');
});

task('nginx:config:move', static function (): void {
	run('cp {{release_path}}/docker/nginx/api.prod.conf /etc/nginx/sites-enabled/api.conf');
	run('cp {{release_path}}/docker/nginx/frontend.prod.conf /etc/nginx/sites-enabled/frontend.conf');
	run('cp {{release_path}}/docker/nginx/static.prod.conf /etc/nginx/sites-enabled/static.conf');
	run('cp {{release_path}}/docker/nginx/nginx.prod.conf /etc/nginx/nginx.conf');
	run('cp {{release_path}}/docker/nginx/php.prod.conf /etc/nginx/php.conf');
	run('cp {{release_path}}/docker/nginx/preflight.conf /etc/nginx/preflight.conf');
	run('cp {{release_path}}/docker/nginx/preflight_headers.conf /etc/nginx/preflight_headers.conf');
	run('cp {{release_path}}/docker/nginx/routes.conf /etc/nginx/routes.conf');
	run('cp {{release_path}}/docker/nginx/security_headers.conf /etc/nginx/security_headers.conf');
	run('cp {{release_path}}/docker/nginx/letsencrypt.conf /etc/nginx/letsencrypt.conf');
});

task('php:config:move', static function (): void {
	run('cp {{release_path}}/docker/php-fpm/php.prod.ini /etc/php/7.3/fpm/php.ini');
	run('cp {{release_path}}/docker/php-fpm/php.prod.ini /etc/php/7.3/cli/php.ini');
});

task('react:build', static function (): void {
	cd('{{release_path}}/frontend');
	run('yarn install');
	run('yarn run build');
});

desc('Deploy your project');
task('deploy', [
	'deploy:info',
	'deploy:prepare',
	'deploy:lock',
	'deploy:release',
	'deploy:update_code',
	'deploy:shared',
	'deploy:writable',
	'migrations:run',
	'composer:install',
	'react:build',
	'deploy:clear_paths',
	'passwords:put',
	'nginx:config:move',
	'php:config:move',
	'deploy:symlink',
	'cache:clear',
	'services:reload',
	'deploy:unlock',
	'cleanup',
	'success',
]);

after('deploy:failed', 'deploy:unlock');
