# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

## Table of contents

- [[Unreleased]](#unreleased)
- [[1.0.0] - 2026-08-27](#100---2026-08-27)

## [Unreleased]

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

[1.0.0]: https://github.com/nowo-tech/BarcodeInputBundle/releases/tag/v1.0.0
