#!/bin/sh
set -e

# FRANKENPHP_MODE: classic | worker (REQ-DEMO-010). Default: worker.
# Set via .env / Compose only — not baked into the image ENV.
MODE="${FRANKENPHP_MODE:-worker}"
# FrankenPHP reads /etc/frankenphp/Caddyfile (see the exec line below); copy the
# mode-specific file from the bind-mounted app so edits apply on restart.
CADDY_SRC_DIR="/app/docker/frankenphp"
CADDY_DEST="/etc/frankenphp/Caddyfile"
case "$MODE" in
	classic)
		if [ -f "$CADDY_SRC_DIR/Caddyfile.dev" ]; then
			cp "$CADDY_SRC_DIR/Caddyfile.dev" "$CADDY_DEST"
		elif [ -f /etc/frankenphp/Caddyfile.dev ]; then
			cp /etc/frankenphp/Caddyfile.dev "$CADDY_DEST"
		fi
		;;
	worker)
		if [ -f "$CADDY_SRC_DIR/Caddyfile" ]; then
			cp "$CADDY_SRC_DIR/Caddyfile" "$CADDY_DEST"
		fi
		# else keep the image default Caddyfile (worker enabled)
		;;
	*)
		echo "Unknown FRANKENPHP_MODE=$MODE (expected classic|worker)" >&2
		exit 1
		;;
esac
echo "FrankenPHP mode: $MODE"

git config --global --add safe.directory /app 2>/dev/null || true
git config --global --add safe.directory /var/barcode-input-bundle 2>/dev/null || true
mkdir -p /app/var/cache /app/var/log
chmod -R 777 /app/var 2>/dev/null || true

if [ ! -f /app/vendor/autoload_runtime.php ]; then composer install --no-interaction; fi
exec frankenphp run --config /etc/frankenphp/Caddyfile --adapter caddyfile
