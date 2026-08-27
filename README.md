# Barcode Input Bundle

[![CI](https://github.com/nowo-tech/BarcodeInputBundle/actions/workflows/ci.yml/badge.svg)](https://github.com/nowo-tech/BarcodeInputBundle/actions/workflows/ci.yml) [![Packagist Version](https://img.shields.io/packagist/v/nowo-tech/barcode-input-bundle.svg?style=flat)](https://packagist.org/packages/nowo-tech/barcode-input-bundle) [![Packagist Downloads](https://img.shields.io/packagist/dt/nowo-tech/barcode-input-bundle.svg)](https://packagist.org/packages/nowo-tech/barcode-input-bundle) [![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE) [![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?logo=php)](https://php.net) [![Symfony](https://img.shields.io/badge/Symfony-6.0%2B%20%7C%207.4%2B%20%7C%208.0%20%7C%208.1%2B-000000?logo=symfony)](https://symfony.com)

> Star **Found this useful?** Install it from Packagist and support the project on GitHub.

Symfony `FormType` for barcode capture with optional camera scanning (ZXing) and hardware wedge scanner support.

![FrankenPHP Friendly Worker Mode](docs/images/frankenphp-friendly.png)

This bundle is **FrankenPHP worker mode friendly**.

## Table of contents

- [Features](#features)
- [Quick usage](#quick-usage)
- [Demo preview](#demo-preview)
- [Documentation](#documentation)
- [Tests and coverage](#tests-and-coverage)
- [License](#license)

## Features

- `BarcodeType::class` for product codes, warehouse labels, inventory scans.
- Optional camera scanner button powered by `@zxing/browser`.
- Compatible with USB/Bluetooth keyboard-wedge barcode scanners.
- Normalizes whitespace and non-printable characters server-side.
- TypeScript + Vite assets in `src/Resources/assets` (named package `nowo_barcode_input`).
- Translations **de, en, es, fr, it, nl, pt**.
- Multi-framework Twig themes (Bootstrap, Tailwind, Foundation, Symfony default).

## Quick usage

```php
use Nowo\BarcodeInputBundle\Form\BarcodeType;

$builder->add('barcode', BarcodeType::class, [
    'enable_scanner' => true,
    'max_length' => 13,
    'formats' => ['ean_13', 'ean_8', 'upc_a'],
    'container_class' => 'nowo-barcode-input__container',
    'input_class' => 'form-control',
    'button_class' => 'btn btn-outline-secondary',
]);
```

The value received in `barcode` is a single normalized string, for example `40170725`.

## Demo preview

![Barcode Input Bundle demo](docs/images/barcode-demo.png)

## Documentation

- [GitHub Actions CI requirements](docs/GITHUB_CI.md)
- [Installation](docs/INSTALLATION.md)
- [Configuration](docs/CONFIGURATION.md)
- [PSR evaluation (REQ-CS-007)](docs/PSR.md)
- [Usage](docs/USAGE.md)
- [Contributing](docs/CONTRIBUTING.md)
- [Code of Conduct](CODE_OF_CONDUCT.md)
- [Changelog](docs/CHANGELOG.md)
- [Upgrading](docs/UPGRADING.md)
- [Release](docs/RELEASE.md)
- [Security](docs/SECURITY.md)
- [Engram](docs/ENGRAM.md)
- [Spec-driven development](docs/SPEC-DRIVEN-DEVELOPMENT.md)
- [GitHub Spec Kit](docs/SPEC-KIT.md)

### Additional documentation

- [Demo notes](docs/DEMO-FRANKENPHP.md)

## Tests and coverage

- PHP: 100%
- TS/JS: 100%
- Python: N/A

## License

This bundle is released under the [MIT License](LICENSE).
