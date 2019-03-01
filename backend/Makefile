.DEFAULT_GOAL := check
.PHONY: lint phpcpd phpstan phpcs phpcbf tests tester-coverage echo-failed-tests validate-composer.lock composer-install, count-postgres-tests generate-routes check-test-extensions generate-nginx-conf check-changed-conf phpstan-test

PHPCS_CACHE_DIR := /tmp/cache
PHPCS_ARGS := --standard=ruleset.xml --extensions=php,phpt --encoding=utf-8 --cache=$(PHPCS_CACHE_DIR)/phpcs --tab-width=4 -sp App Tests
TESTER_ARGS := -o console -s -p php -c Tests/php.ini -l /var/log/nette_tester.log
CHECK_TEST_EXTENSIONS := find Tests/Integration/ -name '*.php' | grep -v '\Test.php$$'

check: validate-composer.lock check-changed-conf check-test-extensions lint phpcpd phpstan phpcs tests count-postgres-tests
ci: validate-composer.lock check-changed-conf check-test-extensions lint phpcpd phpstan phpcs tests count-postgres-tests tester-coverage
init: lint

help:               ## help
	@fgrep -h "##" $(MAKEFILE_LIST) | fgrep -v fgrep | sed -e 's/\\$$//' | sed -e 's/##//'

lint:               ## lint
	vendor/bin/parallel-lint -e php,phpt App Tests

phpstan:            ## phpstan
	vendor/bin/phpstan analyse -l max -c phpstan.neon App
	@make phpstan-test --no-print-directory

phpstan-test:
	PHPSTAN=1 vendor/bin/phpstan analyse -l max -c phpstan.test.neon Tests

phpcs:              ## phpcs
	@mkdir -p $(PHPCS_CACHE_DIR)
	vendor/bin/phpcs $(PHPCS_ARGS)

phpcbf:             ## phpcbf
	vendor/bin/phpcbf $(PHPCS_ARGS)

generate-schemas:   ## generate JSON schemas
	php App/Scheduling/index.php GenerateJsonSchema

check-test-extensions:
	@echo "Checking PHP test extensions..."
	@if $(CHECK_TEST_EXTENSIONS) ; then exit 1 ; else echo "Test filenames are OK" ; fi

tests:              ## tests
	vendor/bin/tester $(TESTER_ARGS) Tests/

count-postgres-tests:
	@printf "Number of PostgreSQL tests: "
	@cat Tests/Postgres/*.sql | grep -c "CREATE FUNCTION tests."
	@printf "Number of PostgreSQL assertions: "
	@cat Tests/Postgres/*.sql | grep -c "PERFORM assert."

tester-coverage:
	vendor/bin/tester $(TESTER_ARGS) -d extension=xdebug.so Tests/ --coverage tester-coverage.xml --coverage-src App/

echo-failed-tests:
	@for i in $(find Tests -name \*.actual); do echo "--- $i"; cat $i; echo; echo; done
	@for i in $(find Tests -name \*.expected); do echo "--- $i"; cat $i; echo; echo; done

validate-composer.lock:
	composer validate --no-check-all --strict

generate-routes:    ## generate nginx routes
	php App/Scheduling/index.php GenerateNginxRoutes

generate-nginx-conf:## generate nginx configs
	php App/Scheduling/index.php GenerateNginxConfiguration

check-changed-conf: ## check changed configs
	php App/Scheduling/index.php CheckChangedConfiguration

cron:               ## run cron tasks
	php App/Scheduling/index.php Cron

composer-install:
	composer install --no-interaction --prefer-dist --no-scripts --no-progress --no-suggest --classmap-authoritative

migration-create:
	@sh migrations/create.sh

migration-run:
	@sh migrations/run.sh

migration-run-new:
	@sh migrations/run-new.sh

migration-list-new:
	@sh migrations/list-new.sh

move-schemas:       ## move JSON schemas
	mkdir -p www/schema/bulletpoint/rating
	mkdir -p www/schema/token
	mkdir -p www/schema/refresh_token
	mkdir -p www/schema/theme
	mkdir -p www/schema/theme/bulletpoint
	mkdir -p www/schema/bulletpoint
	mkdir -p www/schema/tag

	ln -sfn $(PWD)/App/Endpoint/Bulletpoint/Ratings/schema/post.json www/schema/bulletpoint/rating/post.json
	ln -sfn $(PWD)/App/Endpoint/Tokens/schema/post.json www/schema/token/post.json
	ln -sfn $(PWD)/App/Endpoint/RefreshTokens/schema/post.json www/schema/refresh_token/post.json
	ln -sfn $(PWD)/App/Endpoint/Themes/schema/post.json www/schema/theme/post.json
	ln -sfn $(PWD)/App/Endpoint/Theme/Bulletpoints/schema/post.json www/schema/theme/bulletpoint/post.json
	ln -sfn $(PWD)/App/Endpoint/Bulletpoint/schema/get.json www/schema/bulletpoint/get.json
	ln -sfn $(PWD)/App/Endpoint/Tags/schema/get.json www/schema/tag/get.json
	ln -sfn $(PWD)/App/Endpoint/Tags/schema/post.json www/schema/tag/post.json
