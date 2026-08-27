# Code inventory — 100% traceability

**Baseline spec**: [`spec.md`](spec.md)  
**Package**: `nowo-tech/barcode-input-bundle`  
**Last audited**: 2026-08-27

This file proves that **every source artifact** under `src/` is referenced by the baseline specification. Co-located Vitest files enforce frontend contracts; PHPUnit covers PHP under `tests/`.

## PHP classes (`src/**/*.php`)

| Source file | Spec section | Requirement IDs |
| --- | --- | --- |
| `NowoBarcodeInputBundle.php` | Bundle entry | FR-BUNDLE-001 |
| `DependencyInjection/Configuration.php` | Config tree | FR-CFG-001 |
| `DependencyInjection/NowoBarcodeInputExtension.php` | DI extension | FR-CFG-002 |
| `DependencyInjection/Compiler/TwigPathsPass.php` | Twig namespace | FR-TWIG-001 |
| `Form/BarcodeType.php` | Barcode form type | FR-FORM-001 |
| `Form/BarcodeFormat.php` | Symbology enum | FR-FORM-002 |
| `Form/DataTransformer/BarcodeValueTransformer.php` | Model/view transform | FR-XFORM-001 |

## TypeScript production (`src/Resources/assets/src/`)

| Source file | Spec section | Requirement IDs |
| --- | --- | --- |
| `barcode-input.ts` | Standalone IIFE entry | FR-UI-001, FR-LEGACY-001 |
| `barcode-input-lib.ts` | Widget + camera scan | FR-UI-001 |
| `nowo-barcode-input-element.ts` | Custom element | FR-UI-002 |
| `logger.ts` | Debug logging | FR-UI-003 |
| `barcode-input.css` | Widget styles | FR-TWIG-002 |

## Vitest co-located (`src/Resources/assets/src/`)

| Source file | Spec section | Requirement IDs |
| --- | --- | --- |
| `barcode-input-lib.test.ts` | Library contract tests | FR-UI-001 |
| `barcode-input.test.ts` | Entry contract tests | FR-LEGACY-001 |
| `nowo-barcode-input-element.test.ts` | Custom element tests | FR-UI-002 |

## Legacy JavaScript (`src/Resources/public/`)

| Source file | Spec section | Requirement IDs |
| --- | --- | --- |
| `Resources/public/barcode-input.js` | Pre-built IIFE fallback | FR-LEGACY-001 |

## Symfony config (`src/Resources/config/`)

| Source file | Spec section | Requirement IDs |
| --- | --- | --- |
| `Resources/config/services.yaml` | Service wiring | FR-DI-001 |

## Twig form themes (`src/Resources/views/Form/`)

| Source file | Spec section | Requirement IDs |
| --- | --- | --- |
| `barcode_input_theme.html.twig` | Default div layout | FR-TWIG-002 |
| `barcode_input_theme_bootstrap3.html.twig` | Bootstrap 3 | FR-TWIG-003 |
| `barcode_input_theme_bootstrap3_horizontal.html.twig` | Bootstrap 3 horizontal | FR-TWIG-003 |
| `barcode_input_theme_bootstrap4.html.twig` | Bootstrap 4 | FR-TWIG-003 |
| `barcode_input_theme_bootstrap4_horizontal.html.twig` | Bootstrap 4 horizontal | FR-TWIG-003 |
| `barcode_input_theme_bootstrap5.html.twig` | Bootstrap 5 | FR-TWIG-003 |
| `barcode_input_theme_bootstrap5_horizontal.html.twig` | Bootstrap 5 horizontal | FR-TWIG-003 |
| `barcode_input_theme_foundation5.html.twig` | Foundation 5 | FR-TWIG-004 |
| `barcode_input_theme_foundation6.html.twig` | Foundation 6 | FR-TWIG-004 |
| `barcode_input_theme_table.html.twig` | Table layout | FR-TWIG-005 |
| `barcode_input_theme_tailwind2.html.twig` | Tailwind 2 | FR-TWIG-006 |

## Translations (`src/Resources/translations/`)

| Source file | Spec section | Requirement IDs |
| --- | --- | --- |
| `NowoBarcodeInputBundle.en.yaml` | i18n | FR-I18N-001 |
| `NowoBarcodeInputBundle.es.yaml` | i18n | FR-I18N-001 |
| `NowoBarcodeInputBundle.de.yaml` | i18n | FR-I18N-001 |
| `NowoBarcodeInputBundle.fr.yaml` | i18n | FR-I18N-001 |
| `NowoBarcodeInputBundle.it.yaml` | i18n | FR-I18N-001 |
| `NowoBarcodeInputBundle.nl.yaml` | i18n | FR-I18N-001 |
| `NowoBarcodeInputBundle.pt.yaml` | i18n | FR-I18N-001 |

## Coverage summary

| Category | Files | Mapped |
| --- | ---: | ---: |
| PHP classes | 7 | 7 |
| TypeScript production | 5 | 5 |
| Vitest co-located | 3 | 3 |
| Legacy JS | 1 | 1 |
| Symfony config | 1 | 1 |
| Twig themes | 11 | 11 |
| Translations | 7 | 7 |
| **Total** | **35** | **35** |
