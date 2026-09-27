# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

## Table of contents

- [[Unreleased]](#unreleased)
- [[1.0.2] - 2026-09-27](#102---2026-09-27)
- [[1.0.1] - 2026-09-24](#101---2026-09-24)
- [[1.0.0] - 2026-08-27](#100---2026-08-27)

## [Unreleased]

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
