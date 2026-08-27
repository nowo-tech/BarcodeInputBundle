# Configuration

Configuration root: `nowo_barcode_input`

## Table of contents

- [Bundle configuration](#bundle-configuration)
- [Options](#options)
- [form_theme](#form_theme)
- [Supported formats](#supported-formats)
- [Per-field overrides](#per-field-overrides)
- [Assets package](#assets-package)
- [Translations](#translations)
- [Translation overrides](#translation-overrides)

## Bundle configuration

```yaml
# config/packages/nowo_barcode_input.yaml
nowo_barcode_input:
  enable_scanner: true
  max_length: 128
  trim_whitespace: true
  formats: ['ean_13', 'ean_8', 'code_128', 'code_39', 'upc_a']
  facing_mode: environment
  form_theme: 'bootstrap_5_layout.html.twig'
```

## Options

| Key | Type | Default | Description |
| --- | --- | --- | --- |
| `enable_scanner` | bool | `true` | Show the camera scan button in the widget. |
| `max_length` | int | `128` | Maximum barcode length (1–256). |
| `trim_whitespace` | bool | `true` | Trim leading/trailing whitespace on submit. |
| `formats` | list | see above | Supported symbologies for scanner hints. |
| `facing_mode` | string | `environment` | Camera facing mode: `environment` or `user`. |
| `form_theme` | string | `form_div_layout.html.twig` | Twig form theme mapped to bundle themes. |

## form_theme

Supported values:

- `form_div_layout.html.twig`
- `form_table_layout.html.twig`
- `bootstrap_5_layout.html.twig`
- `bootstrap_5_horizontal_layout.html.twig`
- `bootstrap_4_layout.html.twig`
- `bootstrap_4_horizontal_layout.html.twig`
- `bootstrap_3_layout.html.twig`
- `bootstrap_3_horizontal_layout.html.twig`
- `foundation_5_layout.html.twig`
- `foundation_6_layout.html.twig`
- `tailwind_2_layout.html.twig`

## Supported formats

- `ean_13`
- `ean_8`
- `upc_a`
- `upc_e`
- `code_128`
- `code_39`
- `itf`
- `codabar`

## Per-field overrides

See [USAGE.md](USAGE.md) for `BarcodeType` field options.

## Assets package

The bundle registers the Symfony asset package `nowo_barcode_input`:

```twig
<script src="{{ asset('barcode-input.js', 'nowo_barcode_input') }}"></script>
```

## Translations

The bundle ships strings under `src/Resources/translations/` for:

- `en`, `es`, `de`, `fr`, `it`, `nl`, `pt`

Symfony loads them from the `NowoBarcodeInputBundle` domain when the Translator component is enabled in your application. No extra bundle configuration is required.

Keys:

```yaml
scan.button: 'Scan'
scan.close: 'Close'
scan.hint: 'Point the camera at a barcode'
field.placeholder: 'Enter or scan a barcode'
```

## Translation overrides

To override catalogue entries in the host application:

1. Use the same domain: **`NowoBarcodeInputBundle`**.
2. Create an app file such as `translations/NowoBarcodeInputBundle.es.yaml` (or `.xlf`).
3. Override only the keys you need. Missing keys fall back to the bundle catalogue.

Example:

```yaml
# translations/NowoBarcodeInputBundle.es.yaml
scan.button: 'Escanear'
field.placeholder: 'Introduce o escanea un código de barras'
```
