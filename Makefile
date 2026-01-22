COMPOSE = docker compose
APP_DIR = app

.PHONY: up down restart logs ps install migrate console

up:
	$(COMPOSE) up -d

down:
	$(COMPOSE) down

restart: down up

logs:
	$(COMPOSE) logs -f

ps:
	$(COMPOSE) ps

install:
	$(COMPOSE) run --rm composer install

migrate:
	$(COMPOSE) exec php php bin/console doctrine:database:create --if-not-exists
	$(COMPOSE) exec php php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration

console:
	$(COMPOSE) exec php php bin/console
