# Makefile for Symfony with Docker

# Docker commands
.PHONY: up up-auto down down-v restart build logs ps

up: ## Start Docker containers
	sudo systemctl stop postgresql
	docker-compose --env-file .env.local up -d
	@echo "Site web disponible: http://localhost:8080"

up-auto: ## Start containers choosing a free host port for Postgres automatically
	@PORT=$$(bash scripts/pick-free-port.sh $${POSTGRES_PORT:-5433}); \
	echo "Using POSTGRES_PORT=$$PORT"; \
	POSTGRES_PORT=$$PORT docker-compose --env-file .env.local up -d; \
	echo "Database exposed on localhost:$$PORT"; \
	echo "Site web disponible: http://localhost:8080"

down: ## Stop Docker containers
	docker-compose --env-file .env.local down

down-v: ## Stop containers and remove volumes (DB reset)
	docker-compose --env-file .env.local down -v

restart: ## Restart Docker containers
	docker-compose --env-file .env.local restart

build: ## Build Docker containers
	docker-compose --env-file .env.local build

logs: ## Show Docker logs
	docker-compose --env-file .env.local logs -f

ps: ## Show Docker containers status
	docker-compose --env-file .env.local ps

# Symfony commands
.PHONY: install update cache-clear assets migrations fixtures tests reset-db

install: ## Install dependencies
	composer install

update: ## Update dependencies
	composer update

assets: ## Install assets
	docker-compose --env-file .env.local exec web php bin/console asset-map:compile

importmap:
	docker-compose --env-file .env.local exec web php bin/console importmap:install

migrations: ## Run database migrations
	docker-compose --env-file .env.local exec web php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration

fixtures: ## Load fixtures
	docker-compose --env-file .env.local exec web php bin/console doctrine:fixtures:load --no-interaction

tests: ## Run tests
	docker-compose --env-file .env.local exec web php bin/phpunit

# Development commands
.PHONY: lint cs-fix stan

lint: ## Lint PHP files
	docker-compose exec web php -l src/

cs-fix: ## Fix code style
	docker-compose exec web vendor/bin/php-cs-fixer fix

stan: ## Run PHPStan
	docker-compose exec web vendor/bin/phpstan analyse src/

reset-db: ##Reset database and load fixtures
	docker-compose exec web php bin/console doctrine:database:drop --force --if-exists
	docker-compose exec web php bin/console doctrine:database:create
	docker-compose exec web php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration
	docker-compose exec web php bin/console doctrine:fixtures:load --no-interaction

reset-db-hard: ## Reset DB by recreating the Postgres volume, then load fixtures
	$(MAKE) down-v
	$(MAKE) up
	$(MAKE) reset-db

clean: ## Clear Symfony cache
	php bin/console cache:clear
	php bin/console cache:warm
	docker-compose --env-file .env.local exec web php bin/console cache:clear
	docker-compose --env-file .env.local exec web php bin/console cache:warm
	$(MAKE) cs-fix
	$(MAKE) stan

# Help
.PHONY: help

help: ## Display this help message
	@cat $(MAKEFILE_LIST) | grep -e "^[a-zA-Z_\-]*: *.*## *" | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

.DEFAULT_GOAL := help

