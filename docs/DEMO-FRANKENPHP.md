# Demo notes

**REQ-DEMO-001:** FrankenPHP demos must install **Nowo Twig Inspector** and **Nowo Hot Reload** together (`nowo-tech/twig-inspector-bundle` + `nowo-tech/hot-reload-bundle` in `require-dev`). Caddyfile: Mercure + `hot_reload` (and `worker { file …; watch }` in worker mode). Do not enable Hot Reload in production.

## Table of contents

- [Overview](#overview)
- [Switching classic vs worker (`FRANKENPHP_MODE`)](#switching-classic-vs-worker-frankenphp_mode)
- [Demo smoke](#demo-smoke)
- [Start the demo (development)](#start-the-demo-development)
- [Start the demo (production)](#start-the-demo-production)
- [Troubleshooting](#troubleshooting)

## Overview

This bundle includes `demo/symfony8` with a sample Symfony application on **FrankenPHP PHP 8.5** (REQ-DEMO-010).

The demo has its own `docker-compose.yml`, `Dockerfile`, and `docker/frankenphp/` (Caddyfile variants) for local development.

The **repository root** `docker-compose.yml` is for **bundle** development (PHP, Composer, pnpm/Vite, tests). It is not the same as launching a demo as a standalone hosted app.

To run the demo, follow the README inside `demo/symfony8`.

This bundle is **FrankenPHP worker mode friendly** (see the main [README](../README.md) banner and the [worker audit](FRANKENPHP-WORKER-AUDIT.md) for `FRANKENPHP_RESET_KERNEL=false`).

## Switching classic vs worker (`FRANKENPHP_MODE`)

Demos select the FrankenPHP runtime via **`FRANKENPHP_MODE`** in `.env` / `.env.example` (not a Dockerfile `ENV`):

| Value | Behaviour |
| --- | --- |
| **`worker`** (default) | Keep the worker Caddyfile (`php_server { worker ... }`) |
| **`classic`** | Entrypoint copies `Caddyfile.dev` (plain `php_server`, hot-reload friendly) |

Compose passes `FRANKENPHP_MODE=${FRANKENPHP_MODE:-worker}` into the PHP service. After changing `.env`, run `docker compose up -d` (or `make up`) so the container is **recreated** — a plain `restart` does not reload env. No image rebuild is required.

## Demo smoke

From the repository root:

```bash
make demo-smoke
```

This runs `demo/Makefile` `release-verify`: start the Symfony 8 demo, follow redirects from `http://127.0.0.1:$PORT/` and require a final **HTTP 200** (default **8071**), then tear down.

## Start the demo (development)

From the bundle root:

```bash
make -C demo/symfony8 up        # copies .env.example to .env when missing, builds and starts the stack
make -C demo/symfony8 install   # composer install + cache clear (when the Makefile provides it)
```

Then open `http://localhost:$PORT` (`PORT` comes from `demo/symfony8/.env`). For a hot-reload friendly runtime set `FRANKENPHP_MODE=classic` in `.env` (the entrypoint copies `docker/frankenphp/Caddyfile.dev`, plain `php_server` without `worker`) and recreate the container with `docker compose up -d`. Keep `APP_ENV=dev`, `APP_DEBUG=1`, the mounted `docker/php-dev.ini` (`opcache.revalidate_freq=0`) and Twig cache disabled in dev.

## Start the demo (production)

The worker Caddyfile (`docker/frankenphp/Caddyfile`, `php_server { worker { file /app/public/index.php; watch } }`) is the default runtime (`FRANKENPHP_MODE=worker`). To run the demo with a production profile:

1. In `demo/symfony8/.env` set `APP_ENV=prod`, `APP_DEBUG=0`, a real `APP_SECRET` and keep `FRANKENPHP_MODE=worker`.
2. Do not mount `docker/php-dev.ini` (dev-only OPcache revalidation) and do not enable Nowo Hot Reload / Twig Inspector (dev-only bundles).
3. Install without dev dependencies and warm the cache inside the container: `composer install --no-dev --optimize-autoloader` and `php bin/console cache:clear --env=prod`.
4. Recreate the container: `docker compose up -d` (a plain `restart` does not reload environment variables).

Worker mode keeps the kernel booted between requests; see the [worker audit](FRANKENPHP-WORKER-AUDIT.md) when it exists for the services reviewed for request-to-request state.

## Troubleshooting

- **Code or template changes not visible:** worker mode keeps the kernel in memory. Switch to `FRANKENPHP_MODE=classic` (or rely on `watch` in the worker Caddyfile), make sure `docker/php-dev.ini` is mounted and Twig cache is off in dev, then hard-refresh the browser.
- **`FRANKENPHP_MODE` change ignored:** run `docker compose up -d` (or `make up`) so the container is recreated; `docker compose restart` keeps the old environment.
- **Port already in use / demo does not answer:** change `PORT` in `demo/symfony8/.env`, then check `docker compose logs php` for Caddy or PHP errors.
- **Web Profiler toolbar missing:** check `APP_ENV=dev`, `APP_DEBUG=1` and that WebProfilerBundle is enabled for `dev` in `config/bundles.php`; clear the cache with `docker compose exec php php bin/console cache:clear`.
- **Bundle changes not picked up:** the bundle is mounted from the repository root; run `make -C demo/symfony8 update-bundle` (or `composer update nowo-tech/*`) and clear the cache.
