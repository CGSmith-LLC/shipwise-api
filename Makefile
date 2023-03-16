# This is a Makefile that implements helpers for the docker stack
#

CONTAINER_NAME=api
TESTCASE=

.PHONY: cli bash docker-up docker-down lint test help
.DEFAULT_GOAL := help

help: # https://marmelab.com/blog/2016/02/29/auto-documented-makefile.html
	@echo "make implements common cli tasks"
	@echo ""
	@echo "Learn more about make at https://www.gnu.org/software/make/manual/make.html"
	@echo ""
	@echo "The following commands are available:"
	@echo ""
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "make \033[36m%-20s\033[0m %s\n", $$1, $$2}'

# Run a bash in a Docker container as your local user,
# so files are created owned by that user instead of root.
#
# For this to work, the user inside the container must have the same ID as the user outside.
# The name does not matter much but to avoid confusion it is helpful to make them have the same user name.
# You get a users ID by running the `id -u` command, and the users name is `whoami`.
cli: bash
bash: docker-up vendor/autoload.php ## Run a bash in a Docker container as your local user,
	@echo "\nYou are now in the \033[1m$(CONTAINER_NAME)\033[0m container.\n"
	@docker-compose exec --user=$$(id -u) $(CONTAINER_NAME) php -v && echo ""
	@docker-compose exec --user=$$(id -u) $(CONTAINER_NAME) bash

# 1. Start Docker containers
# 2. Create a user with the same name and ID inside the docker conterainer if it does not exists.
#    The user is also added to the www-data group to work well with files created by the webserver.
#    The users home directory is /app/docker/home so bash history files are stored in the docker/home directory in this repo.
docker-up: docker-compose.override.yml api/runtime/docker-build environments/dev/api/web/index.php ## Start Docker containers and prepare Docker environment, auto-rebulids containers when Dockerfile changes
	docker-compose up -d
	docker-compose exec -T $(CONTAINER_NAME) bash -c "grep '^$(shell whoami):' /etc/passwd || useradd '$(shell whoami)' --uid=$(shell id -u) -G www-data -s /bin/bash -d /app/docker/home"

# auto rebuild docker containers when Dockerfile changes
# will change timestamp of runtime/docker-build to only run this when docker files change
api/runtime/docker-build: $(shell find */Dockerfile)
	docker-compose build
	touch $@

# Stop Docker containers
docker-down: ## Stop Docker containers
	docker-compose down --remove-orphans

# Run composer install if vendor/autoload.php does not exist or is outdated (older than composer.json)
vendor/autoload.php: composer.json docker-up
	@docker-compose exec -T --user=$$(id -u) $(CONTAINER_NAME) composer install

# create docker-compose.override.yml if it does not exist
docker-compose.override.yml: docker-compose.override.dist.yml
	test -f $@ || cp $< $@

# run php init on first start
api/web/index.php: environments/dev/api/web/index.php
	test -f $@ || docker-compose exec -T --user=$$(id -u) $(CONTAINER_NAME) php init


test: ## Run codeception tests, run a specific test by setting the TESTCASE variable: make test TESTCASE="acceptance tests/acceptance/NotSignedInCest.php" 
	docker-compose exec -T --user=$$(id -u) $(CONTAINER_NAME) vendor/bin/codecept run $(TESTCASE)


lint: ## Validate composer.{json|lock}
	docker-compose exec -T --user=$$(id -u) $(CONTAINER_NAME) composer validate --strict

