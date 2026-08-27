DEMOS := symfony8

.PHONY: help up down test test-coverage release-check release-verify
.PHONY: $(foreach demo,$(DEMOS),up-$(demo) down-$(demo) update-bundle-$(demo) test-$(demo) test-coverage-$(demo))

help:
	@echo "Barcode Input Bundle Demo"
	@echo ""
	@echo "  up-symfony8 / down-symfony8 / update-bundle-symfony8"
	@echo "  release-verify   up → HTTP 2xx/3xx → down (REQ-TEST-011)"
	@echo "  release-check    Full demo release-check (tests + smoke)"

up-symfony8:
	@$(MAKE) -C symfony8 up

down-symfony8:
	@$(MAKE) -C symfony8 down

update-bundle-symfony8:
	@$(MAKE) -C symfony8 update-bundle

test-symfony8:
	@$(MAKE) -C symfony8 test

test-coverage-symfony8:
	@$(MAKE) -C symfony8 test-coverage

up:
	@[ -n "$(DEMO)" ] || { echo "Usage: make up DEMO=symfony8"; exit 1; }
	@$(MAKE) -C $(DEMO) up

down:
	@[ -n "$(DEMO)" ] || { echo "Usage: make down DEMO=symfony8"; exit 1; }
	@$(MAKE) -C $(DEMO) down

update-bundle:
	@[ -n "$(DEMO)" ] || { echo "Usage: make update-bundle DEMO=symfony8"; exit 1; }
	@$(MAKE) -C $(DEMO) update-bundle

test:
	@[ -n "$(DEMO)" ] || { echo "Usage: make test DEMO=symfony8"; exit 1; }
	@$(MAKE) -C $(DEMO) test

test-coverage:
	@[ -n "$(DEMO)" ] || { echo "Usage: make test-coverage DEMO=symfony8"; exit 1; }
	@$(MAKE) -C $(DEMO) test-coverage

# REQ-TEST-011 / demo-smoke: up → HTTP 2xx/3xx → down
release-verify:
	@for demo in $(DEMOS); do \
		port=$$(grep '^PORT=' $$demo/.env 2>/dev/null | cut -d= -f2 | tr -d '\r'); \
		[ -z "$$port" ] && port=$$(grep '^PORT=' $$demo/.env.example 2>/dev/null | cut -d= -f2 | tr -d '\r'); \
		[ -z "$$port" ] && port=8071; \
		echo "Release verify: $$demo (port $$port)..."; \
		$(MAKE) up-$$demo; \
		code=000; \
		for i in 1 2 3 4 5 6 7 8 9 10 11 12 13 14 15; do \
			code=$$(curl -s -o /dev/null -w "%{http_code}" "http://127.0.0.1:$$port/" 2>/dev/null || echo "000"); \
			echo "$$code" | grep -qE '^[23]' && break; \
			echo "  release-verify: waiting for $$demo... $$i/15 (HTTP $$code)"; \
			sleep 2; \
		done; \
		$(MAKE) down-$$demo 2>/dev/null || true; \
		if ! echo "$$code" | grep -qE '^[23]'; then echo "$$demo healthcheck failed (HTTP $$code)"; exit 1; fi; \
		echo "OK $$demo (HTTP $$code)"; \
	done
	@echo "All demos passed release-verify."

release-check: release-verify
	@$(MAKE) -C symfony8 release-check

BUNDLE_ROOT := $(abspath $(dir $(lastword $(MAKEFILE_LIST)))/..)
# Optional: monorepo helper absent on standalone GitHub Actions checkout (REQ-MAKE-009).
-include $(BUNDLE_ROOT)/../.scripts/Makefile.demo-aggregate-update-deps.mk
