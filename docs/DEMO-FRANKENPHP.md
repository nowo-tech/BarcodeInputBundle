# Demo notes

**REQ-DEMO-001:** FrankenPHP demos must install **Nowo Twig Inspector** and **Nowo Hot Reload** together (`nowo-tech/twig-inspector-bundle` + `nowo-tech/hot-reload-bundle` in `require-dev`). Caddyfile: Mercure + `hot_reload` (and `worker { file …; watch }` in worker mode). Do not enable Hot Reload in production.

## Table of contents

- [Overview](#overview)
- [Switching classic vs worker (`FRANKENPHP_MODE`)](#switching-classic-vs-worker-frankenphp_mode)
- [Demo smoke](#demo-smoke)

## Overview

This bundle includes `demo/symfony8` with a sample Symfony application on **FrankenPHP PHP 8.5** (REQ-DEMO-010).

The demo has its own `docker-compose.yml`, `Dockerfile`, and `docker/frankenphp/` (Caddyfile variants) for local development.

The **repository root** `docker-compose.yml` is for **bundle** development (PHP, Composer, pnpm/Vite, tests). It is not the same as launching a demo as a standalone hosted app.

To run the demo, follow the README inside `demo/symfony8`.

This bundle is **FrankenPHP worker mode friendly** (see the main [README](../README.md) banner).

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

This runs `demo/Makefile` `release-verify`: start the Symfony 8 demo, wait for HTTP 2xx/3xx on `http://127.0.0.1:$PORT/` (default **8071**), then tear down.
