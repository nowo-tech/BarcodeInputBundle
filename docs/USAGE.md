# Usage

## Table of contents

- [Form type](#form-type)
- [Frontend script](#frontend-script)
- [Customization](#customization)
- [Twig template overrides](#twig-template-overrides)

## Form type

Use `BarcodeType` in any Symfony form:

```php
use Nowo\BarcodeInputBundle\Form\BarcodeType;

$builder->add('barcode', BarcodeType::class, [
    'enable_scanner' => true,
    'max_length' => 64,
    'formats' => ['ean_13', 'code_128', 'code_39'],
    'facing_mode' => 'environment',
    'container_class' => 'nowo-barcode-input__container',
    'input_class' => 'form-control',
    'button_class' => 'btn btn-outline-secondary',
    'autofocus' => true,
]);
```

The field value is a single string, for example `40170725`.

## Frontend script

Include the built barcode script in your layout (after `assets:install`):

```twig
<script src="{{ asset('barcode-input.js', 'nowo_barcode_input') }}"></script>
```

The `nowo_barcode_input` package maps to `/bundles/nowobarcodeinput` (the path created by `assets:install`).

The script defines the `<nowo-barcode-input>` custom element and initializes legacy `[data-nowo-barcode-container="1"]` wrappers.

## Customization

- `enable_scanner`: show camera scan button when true
- `max_length`: maximum characters (1–256)
- `trim_whitespace` / `strip_non_printable`: server-side normalization
- `formats`: symbology hints for camera scanning (`ean_13`, `ean_8`, `upc_a`, `code_128`, `code_39`, `itf`, `codabar`)
- `facing_mode`: `environment` (rear) or `user` (front) camera
- `container_class`, `input_class`, `button_class`: CSS hooks
- `autofocus`: focus the text input on load

Hardware wedge scanners work out of the box: they type into the focused text input like a keyboard.

## Twig template overrides

The bundle registers its Twig views so that `@NowoBarcodeInputBundle/...` works, and it adds its view path **after** the application paths. Overrides under **`templates/bundles/NowoBarcodeInputBundle/`** are therefore checked first.

Use the directory name **`NowoBarcodeInputBundle`** (matches `Bundle::getName()`).

| Bundle path under `src/Resources/views/` | Application override |
| ---------------------------------------- | -------------------- |
| `Form/barcode_input_theme.html.twig` | `templates/bundles/NowoBarcodeInputBundle/Form/barcode_input_theme.html.twig` |
| `Form/barcode_input_theme_table.html.twig` | `templates/bundles/NowoBarcodeInputBundle/Form/barcode_input_theme_table.html.twig` |
| `Form/barcode_input_theme_bootstrap5.html.twig` | `templates/bundles/NowoBarcodeInputBundle/Form/barcode_input_theme_bootstrap5.html.twig` |
| `Form/barcode_input_theme_bootstrap5_horizontal.html.twig` | `templates/bundles/NowoBarcodeInputBundle/Form/barcode_input_theme_bootstrap5_horizontal.html.twig` |
| `Form/barcode_input_theme_bootstrap4.html.twig` | `templates/bundles/NowoBarcodeInputBundle/Form/barcode_input_theme_bootstrap4.html.twig` |
| `Form/barcode_input_theme_bootstrap4_horizontal.html.twig` | `templates/bundles/NowoBarcodeInputBundle/Form/barcode_input_theme_bootstrap4_horizontal.html.twig` |
| `Form/barcode_input_theme_bootstrap3.html.twig` | `templates/bundles/NowoBarcodeInputBundle/Form/barcode_input_theme_bootstrap3.html.twig` |
| `Form/barcode_input_theme_bootstrap3_horizontal.html.twig` | `templates/bundles/NowoBarcodeInputBundle/Form/barcode_input_theme_bootstrap3_horizontal.html.twig` |
| `Form/barcode_input_theme_foundation5.html.twig` | `templates/bundles/NowoBarcodeInputBundle/Form/barcode_input_theme_foundation5.html.twig` |
| `Form/barcode_input_theme_foundation6.html.twig` | `templates/bundles/NowoBarcodeInputBundle/Form/barcode_input_theme_foundation6.html.twig` |
| `Form/barcode_input_theme_tailwind2.html.twig` | `templates/bundles/NowoBarcodeInputBundle/Form/barcode_input_theme_tailwind2.html.twig` |

All form-submitting demo and host templates **must** use Symfony Forms (`form_start` / `form_row` / `form_end`), never raw `<form>` or `<input>` in page templates.
