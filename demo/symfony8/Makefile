.PHONY: help up down build install shell logs test test-coverage cache-clear link-bundle update-bundle ensure-up restart release-check

help:
	@echo "Symfony 8 Demo - Barcode Input Bundle"

up:
	@if [ ! -f .env ]; then cp .env.example .env 2>/dev/null || true; fi
	$(COMPOSE) up -d
	@echo "Waiting for container to be ready..."
	@sleep 5
	@echo "Installing dependencies..."
	@$(COMPOSE) exec -T -e COMPOSER_MEMORY_LIMIT=-1 php composer install --no-interaction
	@$(MAKE) link-bundle
	@PORT=$$(grep "^PORT=" .env 2>/dev/null | cut -d= -f2 | tr -d '\r'); \
	[ -z "$$PORT" ] && PORT=$$(grep "^PORT=" .env.example 2>/dev/null | cut -d= -f2 | tr -d '\r'); \
	echo "Demo started at: http://localhost:$$PORT"

down:
	$(COMPOSE) down

restart: ensure-up
	$(COMPOSE) restart

build:
	$(COMPOSE) build --no-cache

ensure-up:
	@if ! $(COMPOSE) exec -T php true 2>/dev/null; then $(COMPOSE) up -d; sleep 3; fi

install: ensure-up
	$(COMPOSE) exec -T -e COMPOSER_MEMORY_LIMIT=-1 php composer install --no-interaction

shell: ensure-up
	$(COMPOSE) exec php sh

logs:
	$(COMPOSE) logs -f

test: ensure-up
	$(COMPOSE) exec -e APP_ENV=test php composer test

test-coverage: ensure-up
	$(COMPOSE) exec php composer test-coverage

cache-clear: ensure-up
	$(COMPOSE) exec php php bin/console cache:clear

link-bundle: ensure-up
	$(COMPOSE) exec php sh -c "rm -rf vendor/nowo-tech/barcode-input-bundle && ln -s /var/barcode-input-bundle vendor/nowo-tech/barcode-input-bundle && php bin/console cache:clear && php bin/console assets:install public"

update-bundle: ensure-up
	$(COMPOSE) exec php composer update nowo-tech/barcode-input-bundle --no-interaction
	$(COMPOSE) exec php sh -c "php bin/console cache:clear --env=dev && php bin/console assets:install public"

release-check: ensure-up update-bundle test test-coverage

# REQ-MAKE-008: update-deps (REQ-MAKE-008)
# Prefer Compose V2 plugin (GitHub Actions / modern Docker Desktop); fall back to docker-compose V1 (REQ-MAKE-010).
COMPOSE := $(shell docker compose version >/dev/null 2>&1 && echo "docker compose" || echo "docker-compose")
SERVICE_PHP := php
# Optional: monorepo helper absent on standalone GitHub Actions checkout (REQ-MAKE-009).
-include $(abspath $(dir $(lastword $(MAKEFILE_LIST)))/../../../.scripts/Makefile.demo-update-deps.mk)
