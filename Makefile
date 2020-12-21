# customization

PACKAGE_NAME = icanboogie/accessor
PACKAGE_VERSION = 4.0
PHPUNIT = vendor/bin/phpunit

# do not edit the following lines

usage:
	@echo "test:  Runs the test suite.\ndoc:   Creates the documentation.\nclean: Removes the documentation, the dependencies and the Composer files."

vendor:
	@composer install

# testing

test-dependencies: vendor

test-container:
	@docker-compose run --rm app sh
	@docker-compose down

test: test-dependencies
	@$(PHPUNIT)

test-coverage: test-dependencies
	@mkdir -p build/coverage
	@$(PHPUNIT) --coverage-html build/coverage --coverage-text

test-coveralls: test-dependencies
	@mkdir -p build/logs
	@$(PHPUNIT) --coverage-clover build/logs/clover.xml

#doc

doc: vendor
	@mkdir -p build/docs
	@apigen generate \
	--source lib \
	--destination build/docs/ \
	--title "$(PACKAGE_NAME) v$(PACKAGE_VERSION)" \
	--template-theme "bootstrap"

# utils

clean:
	@rm -fR build
	@rm -fR vendor
	@rm -f composer.lock

.PHONY: all autoload doc clean test test-coverage test-coveralls test-dependencies update
