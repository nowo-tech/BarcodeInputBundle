# Upgrading

This document describes upgrade notes for `BarcodeInputBundle`.

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
