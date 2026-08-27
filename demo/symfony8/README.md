# Barcode Input Bundle — Demo (Symfony 8)

This demo runs with **FrankenPHP** (Caddy, HTTP on port 80). In **dev** (`APP_ENV=dev`), worker mode is disabled so each request runs in a new PHP process and **code/template changes are visible on refresh** without restarting the container.

## Quick start

```bash
make up
make install
# Open http://localhost:8072 (or set PORT in .env)
```

## Demo URLs

- `/` — Redirects to `/demo`
- `/demo` — Home with links to all barcode examples
- `/demo/barcode/ean-scanner` — EAN scanner (camera + manual entry)
- `/demo/barcode/warehouse-code128` — Warehouse Code 128 labels
- `/demo/barcode/manual-only` — Manual entry only (keyboard wedge scanners)

## Web Profiler toolbar

The demo has **Web Profiler** enabled in `dev`. The toolbar is shown at the bottom of the page when:

- `APP_ENV=dev` and `APP_DEBUG=1` (default in `.env`)
- You have run `make install` and the dev routes are loaded

If the toolbar does not appear, clear the cache inside the container:

```bash
docker compose exec php php bin/console cache:clear --env=dev
```

Then reload the page. You can also open `/_profiler` to see the latest requests.

## Commands

- `make up` — Build and start the container (FrankenPHP). After changing Dockerfile or Caddyfile, run `make build` or `docker compose build` then `make up`.
- `make down` — Stop the container
- `make install` — Composer install (and cache:clear)
- `make shell` — Open a shell in the container
