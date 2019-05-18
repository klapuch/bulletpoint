.DEFAULT_GOAL := check

PHPCS_CACHE_DIR := /tmp/cache
PHPCS_ARGS := --standard=ruleset.xml --extensions=php,phpt --encoding=utf-8 --cache=$(PHPCS_CACHE_DIR)/phpcs --tab-width=4 -sp App Tests www
TESTER_ARGS := -o console -s -p php -c Tests/php.ini -l /var/log/nette_tester.log
CHECK_TEST_EXTENSIONS := find Tests/Integration/ Tests/Unit Tests/Functional -name '*.php' | grep -v '\Test.php$$'

.PHONY: check
check: validate-composer.lock check-changed-conf plpgsql_check check-test-extensions lint phpmnd phpstan psalm phpcs tests count-postgres-tests

.PHONY: ci
ci: check tester-coverage

.PHONY: help
help:               ## help
	@fgrep -h "##" $(MAKEFILE_LIST) | fgrep -v fgrep | sed -e 's/\\$$//' | sed -e 's/##//'

.PHONY: lint
lint:               ## lint
	vendor/bin/parallel-lint -e php,phpt App Tests

.PHONY: phpmnd
phpmnd:             ## phpmnd
	vendor/bin/phpmnd App
	vendor/bin/phpmnd Tests

.PHONY: phpstan
phpstan:            ## phpstan
	vendor/bin/phpstan analyse -l max -c phpstan.neon App
	@make phpstan-test --no-print-directory

.PHONY: phpstan-test
phpstan-test:
	PHPSTAN=1 vendor/bin/phpstan analyse -l max -c phpstan.test.neon Tests

.PHONY: psalm
psalm:              ## psalm
	vendor/bin/psalm

.PHONY: phpcs
phpcs:              ## phpcs
	@mkdir -p $(PHPCS_CACHE_DIR)
	vendor/bin/phpcs $(PHPCS_ARGS)

.PHONY: phpcbf
phpcbf:             ## phpcbf
	@mkdir -p $(PHPCS_CACHE_DIR)
	vendor/bin/phpcbf $(PHPCS_ARGS)

.PHONY: generate-schemas
generate-schemas:   ## generate JSON schemas
	php App/Scheduling/index.php GenerateJsonSchema

.PHONY: check-test-extensions
check-test-extensions:
	@echo "Checking PHP test extensions..."
	@if $(CHECK_TEST_EXTENSIONS) ; then exit 1 ; else echo "Test filenames are OK" ; fi

.PHONY: tests
tests:              ## tests
	vendor/bin/tester $(TESTER_ARGS) Tests/

.PHONY: count-postgres-tests
count-postgres-tests:
	@printf "Number of PostgreSQL tests: "
	@cat Tests/Postgres/*.sql | grep -c "CREATE FUNCTION tests."
	@printf "Number of PostgreSQL assertions: "
	@cat Tests/Postgres/*.sql | grep -c "PERFORM assert."

.PHONY: tester-coverage
tester-coverage:
	vendor/bin/tester $(TESTER_ARGS) -d extension=xdebug.so Tests/ --coverage tester-coverage.xml --coverage-src App/

.PHONY: echo-failed-tests
echo-failed-tests:
	@for i in $(find Tests -name \*.actual); do echo "--- $i"; cat $i; echo; echo; done
	@for i in $(find Tests -name \*.expected); do echo "--- $i"; cat $i; echo; echo; done

.PHONY: validate-composer.lock
validate-composer.lock:
	composer validate --no-check-all --strict

.PHONY: generate-routes
generate-routes:    ## generate nginx routes
	php App/Scheduling/index.php GenerateNginxRoutes

.PHONY: generate-nginx-conf
generate-nginx-conf:## generate nginx configs
	php App/Scheduling/index.php GenerateNginxConfiguration

.PHONY: check-changed-conf
check-changed-conf: ## check changed configs
	php App/Scheduling/index.php CheckChangedConfiguration

.PHONY: cron
cron:               ## run cron tasks
	php App/Scheduling/index.php Cron

.PHONY: plpgsql_check
plpgsql_check:      ## run plpgsql_check
	php App/Scheduling/index.php PlPgSqlCheck

.PHONY: composer-install
composer-install:
	composer install --no-interaction --prefer-dist --no-scripts --no-progress --no-suggest --classmap-authoritative

.PHONY: migration-create
migration-create:
	@sh migrations/create.sh

.PHONY: migration-run
migration-run:
	@sh migrations/run.sh $(name)

.PHONY: migration-run-new
migration-run-new:
	@sh migrations/run-new.sh

.PHONY: migration-list-new
migration-list-new:
	@sh migrations/list-new.sh

.PHONY: move-schemas
move-schemas:       ## move JSON schemas
	mkdir -p www/schema/bulletpoint
	mkdir -p www/schema/bulletpoint/rating
	mkdir -p www/schema/refresh_token
	mkdir -p www/schema/tag
	mkdir -p www/schema/theme
	mkdir -p www/schema/theme/bulletpoint
	mkdir -p www/schema/token
	mkdir -p www/schema/user

	ln -sfn $(PWD)/App/Endpoint/Bulletpoint/Ratings/schema/post.json www/schema/bulletpoint/rating/post.json
	ln -sfn $(PWD)/App/Endpoint/Bulletpoint/schema/get.json www/schema/bulletpoint/get.json
	ln -sfn $(PWD)/App/Endpoint/RefreshTokens/schema/post.json www/schema/refresh_token/post.json
	ln -sfn $(PWD)/App/Endpoint/Tags/schema/get.json www/schema/tag/get.json
	ln -sfn $(PWD)/App/Endpoint/Tags/schema/post.json www/schema/tag/post.json
	ln -sfn $(PWD)/App/Endpoint/Theme/Bulletpoints/schema/post.json www/schema/theme/bulletpoint/post.json
	ln -sfn $(PWD)/App/Endpoint/Themes/schema/post.json www/schema/theme/post.json
	ln -sfn $(PWD)/App/Endpoint/Tokens/schema/post.json www/schema/token/post.json
	ln -sfn $(PWD)/App/Endpoint/Users/schema/put.json www/schema/user/put.json
