# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

## Table of contents

- [[Unreleased]](#unreleased)
- [[1.0.3] - 2026-10-09](#103---2026-10-09)
- [[1.0.2] - 2026-09-27](#102---2026-09-27)
- [[1.0.1] - 2026-09-24](#101---2026-09-24)
- [[1.0.0] - 2026-08-27](#100---2026-08-27)

## [Unreleased]

### Changed

- Development: `composer.json` pins `config.platform.php` to 8.2.0 so the committed lock stays installable on the minimum PHP; CI overrides the platform per matrix cell.

## [1.0.3] - 2026-10-09

### Added

- **REQ-DEMO-013:** Playwright e2e under `demo/symfony8/e2e/` (`make test-e2e`), `demo-screenshots` target, and README gallery cropped to <nowo-barcode-input> (`docs/images/demo/overview.png`, `interaction.png`).

### Fixed

- `pnpm-lock.yaml` re-synced with `package.json` (`@types/node` ^26.6.4) so frozen-lockfile installs work again.

### Dependencies

- Bundle: Symfony 7.4 components -> v7.4.20, polyfills -> v1.43.0, `twig/twig` v3.30.0.
- Dev tooling: `phpstan/phpstan` 2.3.1, `phpstan/phpstan-phpunit` 2.1.1, `phpstan/phpstan-symfony` 2.1.0, `rector/rector` 2.7.0, `phpunit/phpunit` 11.5.57, `friendsofphp/php-cs-fixer` 3.95.27, `igor-php/igor-php` ^0.10 (v0.10.1), `nowo-tech/phpstan-frankenphp` v1.2.3.
- JS dev: Vite 8.3.2, `@types/node` 26.6.4.
- Demo (Symfony 8): Symfony v8.1.8, `twig/extra-bundle` v3.29.0, `nowo-tech/hot-reload-bundle` v1.5.4, `nowo-tech/twig-inspector-bundle` v1.1.7.

[1.0.3]: https://github.com/nowo-tech/BarcodeInputBundle/releases/tag/v1.0.3

## [1.0.2] - 2026-09-27

### Added

- **REQ-CS-008:** `igor-php/igor-php` (require-dev only), root `igor.json`, Composer/`Makefile` `igor` target, and `release-check` wiring for FrankenPHP worker-state audit.

[1.0.2]: https://github.com/nowo-tech/BarcodeInputBundle/releases/tag/v1.0.2

## [1.0.1] - 2026-09-24

### Added

- FrankenPHP worker audit documentation for sticky Kernel (`FRANKENPHP_RESET_KERNEL=false`): [FRANKENPHP-WORKER-AUDIT.md](FRANKENPHP-WORKER-AUDIT.md).
- Unit regression covering consecutive form builds on a shared `BarcodeType` instance (worker / no kernel reset).

### Changed

- `BarcodeType` copies the default `formats` array in `configureOptions()` so OptionsResolver never aliases the shared service array under FrankenPHP worker mode.
- `BarcodeValueTransformer` is a `readonly` class (equivalent immutability; properties remain constructor-injected).
- PHPStan now uses `nowo-tech/phpstan-frankenphp` `ruleset-worker-strict.neon` (includes worker rules).

### Fixed

- None (hardening and documentation only; no user-facing behaviour change).

## [1.0.0] - 2026-08-27

### Added

- Initial release of `nowo-tech/barcode-input-bundle`.
- Symfony `BarcodeType` form field with optional camera scanner (`@zxing/browser`) and keyboard-wedge scanner support.
- `BarcodeValueTransformer` for server-side normalization (trim, strip non-printable, max length).
- `BarcodeFormat` enum for supported symbologies (EAN-13/8, UPC-A/E, Code 128/39, ITF, Codabar).
- TypeScript assets (`barcode-input.js`) with `<nowo-barcode-input>` custom element and standalone IIFE.
- Multi-framework Twig form themes (Bootstrap 3/4/5, Foundation 5/6, Tailwind 2, table, Symfony default).
- Translations: **de, en, es, fr, it, nl, pt**.
- Named Symfony asset package `nowo_barcode_input` (`base_path` `/bundles/nowobarcodeinput`).
- Symfony Flex recipe and Symfony 8 FrankenPHP demo.

[1.0.1]: https://github.com/nowo-tech/BarcodeInputBundle/releases/tag/v1.0.1
[1.0.0]: https://github.com/nowo-tech/BarcodeInputBundle/releases/tag/v1.0.0
