.PHONY: help up down build shell ensure-up install assets assets-test test test-coverage coverage-check test-ts cs-check cs-fix phpstan igor rector rector-dry qa composer-sync release-check release-check-demos demo-smoke validate-translations clean update validate check-no-cursor-coauthor check-open-prs strip-cursor-coauthor-from-history check-twig-extra check-no-raw-html-form setup-hooks twig-lint

COMPOSE_FILE ?= docker-compose.yml
# Prefer Compose V2 plugin (GitHub Actions / modern Docker Desktop); fall back to docker-compose V1 (REQ-MAKE-010).
COMPOSE_BIN ?= $(shell docker compose version >/dev/null 2>&1 && echo "docker compose" || echo "docker-compose")
COMPOSE     ?= $(COMPOSE_BIN) -f $(COMPOSE_FILE)
SERVICE_PHP ?= php

.DEFAULT_GOAL := help

help:
	@echo "BarcodeInputBundle - Development Commands (Docker)"
	@echo ""
	@echo "Usage: make <target>"
	@echo ""
	@echo "Container:"
	@echo "  up             Build and start the container, install Composer + pnpm deps"
	@echo "  down           Stop the container"
	@echo "  build          Rebuild the Docker image (no cache)"
	@echo "  shell          Open a shell in the PHP container"
	@echo "  ensure-up      Start the container if needed (+ install deps)"
	@echo ""
	@echo "Dependencies:"
	@echo "  install        Install Composer and pnpm dependencies"
	@echo ""
	@echo "Assets:"
	@echo "  assets         Build frontend assets (pnpm + Vite)"
	@echo "  assets-test    Alias of test-ts"
	@echo ""
	@echo "Tests:"
	@echo "  test           Run PHPUnit tests"
	@echo "  test-coverage  Run PHPUnit with coverage (console)"
	@echo "  coverage-check Fail if PHP Lines coverage is under 99%%"
	@echo "  test-ts        Run Vitest with coverage (90%% thresholds)"
	@echo ""
	@echo "Quality:"
	@echo "  cs-check       Check code style (PHP-CS-Fixer, dry-run)"
	@echo "  cs-fix         Fix code style"
	@echo "  rector         Apply Rector refactoring"
	@echo "  rector-dry     Rector dry-run (no changes)"
	@echo "  phpstan        Run PHPStan static analysis"
	@echo "  igor           Run Igor worker-state audit (REQ-CS-008)"
	@echo "  qa             cs-check + twig-lint + test"
	@echo "  twig-lint      Lint Twig templates (twig-cs-fixer)"
	@echo "  validate-translations  Translation key parity + YAML parse (REQ-MAKE-004)"
	@echo ""
	@echo "Release:"
	@echo "  release-check  Full pre-release chain (REQ-MAKE-002)"
	@echo "  composer-sync  Validate composer.json and align composer.lock (no install)"
	@echo "  release-check-demos  Demo release-check (tests + smoke)"
	@echo "  demo-smoke     Demo smoke: boot each demo and require HTTP 200 (REQ-TEST-011)"
	@echo "  check-open-prs Fail if unresolved open PRs (REQ-REL-003)"
	@echo "  check-no-cursor-coauthor  Fail on AI co-author trailers (REQ-GIT-001)"
	@echo "  check-twig-extra          Twig Extra installed/configured (REQ-TWIG-004)"
	@echo "  check-no-raw-html-form    No raw <form>/<input> in Twig (REQ-TWIG-005)"
	@echo "  setup-hooks    Install git hooks from .githooks/ (REQ-MAKE-006)"
	@echo ""
	@echo "Cleanup:"
	@echo "  clean          Remove vendor, cache, coverage"
	@echo ""
	@echo "Composer:"
	@echo "  update         Update Composer dependencies (bundle container only)"
	@echo "  update-deps    Update bundle + demo dependencies (REQ-MAKE-008, monorepo helper)"
	@echo "  validate       Validate composer.json (composer validate --strict)"
	@echo ""
	@echo "Demos:"
	@echo "  make -C demo   Demo targets (up-symfony8, down-symfony8, release-verify, ...)"
	@echo ""

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

# Run Igor worker-state audit (REQ-CS-008)
igor: ensure-up
	$(COMPOSE) exec -T php composer igor
check-no-raw-html-form:
	@chmod +x .scripts/check-no-raw-html-form.sh
	@./.scripts/check-no-raw-html-form.sh

release-check:
	@$(MAKE) ensure-up
	@$(MAKE) check-no-cursor-coauthor
	@$(MAKE) check-open-prs
	@$(MAKE) check-twig-extra
	@$(MAKE) check-no-raw-html-form
	@$(MAKE) composer-sync
	@$(MAKE) cs-fix
	@$(MAKE) cs-check
	@$(MAKE) rector-dry
	@$(MAKE) phpstan
	@$(MAKE) igor
	@$(MAKE) validate-translations
	@$(MAKE) coverage-check
	@$(MAKE) release-check-demos
	@$(MAKE) test-ts

clean:
	rm -rf vendor coverage coverage-ts .phpunit.cache coverage-php.txt coverage-ts.txt

update: ensure-up
	$(COMPOSE) exec -T $(SERVICE_PHP) composer update --no-interaction

validate: ensure-up
	$(COMPOSE) exec -T $(SERVICE_PHP) composer validate --strict

validate-translations: ensure-up
	python3 .scripts/check-translation-key-parity.py
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
