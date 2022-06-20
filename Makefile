-include .env.docker.local

DOCKER_COMPOSE:=docker-compose
DOCKER_COMPOSE_RUN:=$(DOCKER_COMPOSE) run --rm --no-deps
DOCKER_COMPOSE_EXEC:=$(DOCKER_COMPOSE) exec
DOCKER_SYMFONY_CONSOLE:=$(DOCKER_COMPOSE_RUN) php php bin/console
USERID:=$(shell id -u)pp
GROUPID:=$(shell id -g)

help: ## Show this help.
	@fgrep -h "##" $(MAKEFILE_LIST) | fgrep -v fgrep | sed -e 's/\\$$//' | sed -e 's/##//' | grep -v '###'

-wait-db:
	$(shell sleep 5)

build: ## Build containers
	eval "$(ssh-agent)"
	DOCKER_BUILDKIT=1 $(DOCKER_COMPOSE) build --build-arg USERID=$(USERID) --build-arg GROUPID=$(GROUPID)

clean: ## Clean everything
	$(DOCKER_COMPOSE) down -v --remove-orphans
	rm -rf ./vendor
	rm -rf ./var/cache/*
	rm -rf ./var/log/*
	rm -rf ./.ecs_cache

install: build install-vendor start -wait-db migrations-run ## install project

install-vendor: ## Install composer dependencies
	$(DOCKER_COMPOSE_RUN) php composer install --prefer-dist -vv

reload-database: ## Reload database
	$(DOCKER_SYMFONY_CONSOLE) doctrine:database:drop --force --if-exists --env=dev
	$(DOCKER_SYMFONY_CONSOLE) doctrine:database:create --if-not-exists --env=dev
	$(MAKE) migrations-run

migrations-run: ## Run migrations
	$(DOCKER_SYMFONY_CONSOLE) doc:migration:migrate --all-or-nothing -n --allow-no-migration

migrations-create: ## Create a migration with current database diff
	$(DOCKER_SYMFONY_CONSOLE) make:migration -n

ssh: ## Log into php container
	$(DOCKER_COMPOSE_EXEC) php ash

start: ## Start containers
	$(DOCKER_COMPOSE) up -d
	@echo "Api accessible here http://127.0.0.1"

stop: ## Stop containers
	$(DOCKER_COMPOSE) stop

restart: ## Restart containers
	$(DOCKER_COMPOSE) restart
