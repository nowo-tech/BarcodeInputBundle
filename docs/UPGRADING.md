# Upgrading


## Unreleased

## To 1.0.3

From **1.0.2** — dependency updates and demo Playwright e2e (REQ-DEMO-013).

```bash
composer update nowo-tech/barcode-input-bundle
```

- No breaking changes. No application upgrade steps.

## To 1.0.2

From **1.0.1** — REQ-CS-008 Igor FrankenPHP worker audit (igor-php require-dev, igor.json, make igor).

```bash
composer update nowo-tech/barcode-input-bundle
php bin/console cache:clear
```

- No application upgrade steps for require-dev Igor tooling (REQ-CS-008). Consumers do not pull `igor-php/igor-php` transitively.

This document describes upgrade notes for `BarcodeInputBundle`.

## 1.0.1

No breaking changes. Upgrade with a normal Composer update:

```bash
composer update nowo-tech/barcode-input-bundle
```

### Notable (non-breaking)

- Confirmed compatible with FrankenPHP **worker** mode when the host runs with **`FRANKENPHP_RESET_KERNEL=false`** (sticky Kernel). See [FRANKENPHP-WORKER-AUDIT.md](FRANKENPHP-WORKER-AUDIT.md).
- Internal: default `formats` passed to the OptionsResolver are copied so a shared `BarcodeType` service cannot leak array state across requests. `BarcodeValueTransformer` is now a `readonly` class. Form options and Twig/JS APIs are unchanged.

No config key, form option, asset package, or theme renames.

## 1.0.0 (initial release)

First public release. There are no prior versions to migrate from.

### Integration checklist

- Package: `nowo-tech/barcode-input-bundle`
- Main form type: `Nowo\BarcodeInputBundle\Form\BarcodeType`
- Root config key: `nowo_barcode_input`
- Asset package name: `nowo_barcode_input` (maps to `/bundles/nowobarcodeinput`)

**Recommended:** load the barcode script with the named asset package:

```twig
<script src="{{ asset('barcode-input.js', 'nowo_barcode_input') }}"></script>
```

Hard-coded paths such as `/bundles/nowobarcodeinput/barcode-input.js` still work after `assets:install`, but the package name is the supported integration.

See [Installation](INSTALLATION.md) and [Configuration](CONFIGURATION.md) for full setup.
