.PHONY: up down build shell ensure-up install assets assets-test test test-coverage coverage-check test-ts cs-check cs-fix phpstan rector rector-dry qa composer-sync release-check release-check-demos demo-smoke validate-translations clean update validate check-no-cursor-coauthor check-open-prs strip-cursor-coauthor-from-history check-twig-extra

COMPOSE_FILE ?= docker-compose.yml
# Prefer Compose V2 plugin (GitHub Actions / modern Docker Desktop); fall back to docker-compose V1 (REQ-MAKE-010).
COMPOSE_BIN ?= $(shell docker compose version >/dev/null 2>&1 && echo "docker compose" || echo "docker-compose")
COMPOSE     ?= $(COMPOSE_BIN) -f $(COMPOSE_FILE)
SERVICE_PHP ?= php

up:
	$(COMPOSE) build
	$(COMPOSE) up -d
	@sleep 3
	$(COMPOSE) exec -T $(SERVICE_PHP) composer install --no-interaction
	$(COMPOSE) exec -T -e CI=true $(SERVICE_PHP) pnpm install

down:
	$(COMPOSE) down

build:
	$(COMPOSE) build --no-cache

shell: ensure-up
	$(COMPOSE) exec $(SERVICE_PHP) sh

ensure-up:
	@if ! $(COMPOSE) exec -T $(SERVICE_PHP) true 2>/dev/null; then \
		$(COMPOSE) up -d; sleep 3; \
		$(COMPOSE) exec -T $(SERVICE_PHP) composer install --no-interaction; \
		$(COMPOSE) exec -T -e CI=true $(SERVICE_PHP) pnpm install; \
	fi

install: ensure-up
	$(COMPOSE) exec -T $(SERVICE_PHP) composer install
	$(COMPOSE) exec -T -e CI=true $(SERVICE_PHP) pnpm install

assets: ensure-up
	$(COMPOSE) exec -T $(SERVICE_PHP) pnpm run build

test: ensure-up
	$(COMPOSE) exec -T $(SERVICE_PHP) composer test

test-coverage: ensure-up
	$(COMPOSE) exec -T $(SERVICE_PHP) composer test-coverage | tee coverage-php.txt
	./.scripts/php-coverage-percent.sh coverage-php.txt

coverage-check: test-coverage
	@chmod +x .scripts/coverage-fail-under.sh
	@./.scripts/coverage-fail-under.sh coverage-php.txt 99

test-ts: ensure-up
	$(COMPOSE) exec -T $(SERVICE_PHP) pnpm run test:coverage | tee coverage-ts.txt
	./.scripts/ts-coverage-percent.sh coverage-ts.txt

assets-test: test-ts

cs-check: ensure-up
	$(COMPOSE) exec -T $(SERVICE_PHP) composer cs-check

cs-fix: ensure-up
	$(COMPOSE) exec -T $(SERVICE_PHP) composer cs-fix

phpstan: ensure-up
	$(COMPOSE) exec -T $(SERVICE_PHP) composer phpstan

rector: ensure-up
	$(COMPOSE) exec -T $(SERVICE_PHP) composer rector

rector-dry: ensure-up
	$(COMPOSE) exec -T $(SERVICE_PHP) composer rector-dry

qa: cs-check twig-lint test

composer-sync: ensure-up
	$(COMPOSE) exec -T $(SERVICE_PHP) composer validate --strict
	$(COMPOSE) exec -T $(SERVICE_PHP) composer update --lock --no-install --no-interaction

release-check-demos:
	@if [ -d demo ]; then $(MAKE) -C demo release-check; fi

demo-smoke:
	@if [ -f demo/Makefile ]; then $(MAKE) -C demo release-verify; else echo "No demo/Makefile"; exit 1; fi

check-open-prs:
	@chmod +x .scripts/check-open-prs.sh
	@GH_REPO=nowo-tech/BarcodeInputBundle ./.scripts/check-open-prs.sh


check-twig-extra:
	@chmod +x .scripts/check-twig-extra.sh
	@./.scripts/check-twig-extra.sh
release-check: check-no-cursor-coauthor check-open-prs check-twig-extra ensure-up composer-sync cs-fix cs-check rector-dry phpstan coverage-check release-check-demos test-ts

clean:
	rm -rf vendor coverage coverage-ts .phpunit.cache coverage-php.txt coverage-ts.txt

update: ensure-up
	$(COMPOSE) exec -T $(SERVICE_PHP) composer update --no-interaction

validate: ensure-up
	$(COMPOSE) exec -T $(SERVICE_PHP) composer validate --strict

validate-translations: ensure-up
	$(COMPOSE) exec -T $(SERVICE_PHP) php -r 'require "vendor/autoload.php"; foreach (glob("src/Resources/translations/*.yaml") as $$f) { Symfony\Component\Yaml\Yaml::parseFile($$f); } echo "OK\n";'


setup-hooks:
	@chmod +x .githooks/pre-commit 2>/dev/null || true
	@chmod +x .githooks/commit-msg 2>/dev/null || true
	@git config core.hooksPath .githooks
	@echo "✅ Git hooks installed (.githooks — includes commit-msg for REQ-GIT-001)."

# REQ-MAKE-008: update-deps (REQ-MAKE-008)
BUNDLE_ROOT := $(abspath $(dir $(lastword $(MAKEFILE_LIST))))
# Optional: monorepo helper absent on standalone GitHub Actions checkout (REQ-MAKE-009).
-include $(BUNDLE_ROOT)/../.scripts/Makefile.update-deps.mk
check-no-cursor-coauthor:
	@chmod +x .scripts/check-no-cursor-coauthor.sh
	@./.scripts/check-no-cursor-coauthor.sh HEAD

strip-cursor-coauthor-from-history:
	@chmod +x .scripts/strip-cursor-coauthor-from-history.sh
	@./.scripts/strip-cursor-coauthor-from-history.sh master

twig-lint: ensure-up
	@$(COMPOSE) exec -T $(SERVICE_PHP) composer twig:lint || $(COMPOSE) exec -T $(SERVICE_PHP) ./vendor/bin/twig-cs-fixer lint --config=.twig-cs-fixer.php
