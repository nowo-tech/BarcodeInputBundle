# Feature Specification: BarcodeInputBundle baseline (100% code coverage)

**Feature Branch**: `001-baseline`  
**Created**: 2026-08-27  
**Status**: Active  
**Input**: Backfill GitHub Spec Kit baseline documenting 100% of production code in `src/`.

**Related docs**: [`docs/SPEC-DRIVEN-DEVELOPMENT.md`](../../docs/SPEC-DRIVEN-DEVELOPMENT.md), [`docs/CONFIGURATION.md`](../../docs/CONFIGURATION.md), [`docs/USAGE.md`](../../docs/USAGE.md)  
**Code inventory (traceability)**: [`code-inventory.md`](code-inventory.md)

---

## Summary

**Package**: `nowo-tech/barcode-input-bundle`  
**Configuration root**: `nowo_barcode_input`

Symfony `BarcodeType` form field for barcode capture with optional camera scanning (ZXing), hardware wedge scanner support, server-side normalization, and multi-framework Twig themes.

---

## User Scenarios & Testing

### User Story 1 — Manual barcode entry (Priority: P1)

As a form author, I use `BarcodeType` so users enter product or warehouse barcodes in a single text field while Symfony receives a normalized string.

**Independent Test**: Render barcode field → type value → submit form → model receives trimmed normalized string.

**Acceptance Scenarios**:

1. **Given** default options, **When** widget renders, **Then** text input with `data-controller="nowo-barcode-input"` and optional scan button appear.
2. **Given** user types a barcode, **When** form submits, **Then** `BarcodeValueTransformer::reverseTransform` returns normalized string.
3. **Given** `max_length=13`, **When** longer value submitted, **Then** value truncated server-side.

---

### User Story 2 — Camera scanner (Priority: P1)

As a user on mobile or desktop, I scan a barcode with the device camera when `enable_scanner` is true.

**Acceptance Scenarios**:

1. **Given** `enable_scanner=true`, **When** user clicks scan button, **Then** camera overlay opens and decoded value fills the input.
2. **Given** scan succeeds, **When** barcode detected, **Then** input emits `input`/`change` events and overlay closes.
3. **Given** camera permission denied, **When** scan fails, **Then** overlay closes gracefully without breaking the form.

---

### User Story 3 — Hardware wedge scanners (Priority: P2)

As a warehouse operator, I use a USB/Bluetooth keyboard-wedge scanner that types into the focused input.

**Acceptance Scenarios**:

1. **Given** manual-only mode (`enable_scanner=false`), **When** wedge scanner sends keystrokes, **Then** characters appear in the text input normally.
2. **Given** user blurs the field, **When** value contains whitespace or non-printable chars, **Then** client normalizes before submit.

---

### User Story 4 — Configure formats and limits (Priority: P2)

As an integrator, I configure global defaults and per-field overrides for scanner, formats, and max length.

**Acceptance Scenarios**:

1. **Given** `formats=['ean_13','ean_8']`, **When** widget renders, **Then** formats passed to frontend as data attributes.
2. **Given** `max_length` outside 1–256, **When** form options validated, **Then** `OptionsResolver` rejects invalid value.
3. **Given** bundle config in `nowo_barcode_input.yaml`, **When** field omits options, **Then** defaults from DI apply.

---

### User Story 5 — Multi-framework themes (Priority: P3)

As an integrator, I pick a Twig form theme matching Bootstrap, Foundation, Tailwind, or table layouts.

**Acceptance Scenarios**:

1. **Given** `form_theme` in bundle config, **When** matches app themes, **Then** correct barcode markup block renders input, scan button, and scanner overlay.
2. **Given** custom `container_class`, `input_class`, `button_class`, **When** set on field, **Then** passed to Twig vars for styling hooks.

---

### Edge Cases

- Scanner overlay open on page unload: active camera stream stopped.
- Disabled field: input and scan button respect Symfony `disabled` state.
- Empty submission: transformer returns empty string (required validation handled by Symfony).
- Container without input element: JS logs warning and skips init (graceful no-op).

---

## Requirements

### Bundle & DI

- **FR-BUNDLE-001**: `NowoBarcodeInputBundle` MUST register `TwigPathsPass` and expose alias `nowo_barcode_input`.
- **FR-DI-001**: `services.yaml` MUST wire `BarcodeType` with default parameters from config.
- **FR-CFG-001**: `Configuration` MUST define: `enable_scanner`, `max_length` (1–256), `trim_whitespace`, `formats`, `facing_mode`, `form_theme`.
- **FR-CFG-002**: Extension MUST load services, inject config defaults into form type, prepend Twig theme and asset package.
- **FR-TWIG-001**: `TwigPathsPass` MUST add bundle views namespace for theme overrides.

### Form type & transformer

- **FR-FORM-001**: `BarcodeType` MUST extend `TextType`, add `BarcodeValueTransformer`, expose barcode vars and Stimulus-style `data-nowo-barcode-input-*` attributes.
- **FR-FORM-002**: `BarcodeFormat` MUST enumerate supported symbologies and validate format strings.
- **FR-XFORM-001**: `BarcodeValueTransformer` MUST normalize model string (trim, strip non-printable, enforce max length).

### Frontend (TypeScript)

- **FR-UI-001**: `barcode-input-lib.ts` MUST initialize containers: normalize on blur, optional camera scan via `@zxing/browser`, dispatch bubbling events.
- **FR-UI-002**: `nowo-barcode-input-element.ts` MUST define `<nowo-barcode-input>` custom element wrapping the widget.
- **FR-UI-003**: `logger.ts` MUST provide namespaced debug logger with build-time metadata.
- **FR-LEGACY-001**: Committed `barcode-input.js` MUST remain loadable without Vite build for downstream consumers.

### Twig themes

- **FR-TWIG-002**: Base `barcode_input_theme.html.twig` MUST render input, scan button, scanner overlay, and load JS asset.
- **FR-TWIG-003**: Bootstrap 3/4/5 (+ horizontal) themes MUST wrap barcode widget with framework form markup.
- **FR-TWIG-004**: Foundation 5/6 themes MUST align with foundation form patterns.
- **FR-TWIG-005**: Table theme MUST render barcode row in table layout.
- **FR-TWIG-006**: Tailwind 2 theme MUST expose utility-class hooks.

### Internationalization

- **FR-I18N-001**: Translation YAML files MUST provide scan button, overlay, and placeholder strings for shipped locales under domain `NowoBarcodeInputBundle`.

---

## Success Criteria

- **SC-001**: **35/35** files under `src/` mapped in [`code-inventory.md`](code-inventory.md).
- **SC-002**: Config keys match `Configuration.php` and `docs/CONFIGURATION.md`.
- **SC-003**: PHPUnit + PHPStan + Vitest pass (`composer qa`, `make test-ts`).
- **SC-004**: Camera scan fills input and produces correct normalized value in browser tests.
- **SC-005**: Model always receives normalized string independent of scan method.

---

## Configuration reference (normative defaults)

| Key | Default | Behavior |
| --- | --- | --- |
| `enable_scanner` | `true` | Show camera scan button |
| `max_length` | `128` | Maximum barcode length (1–256) |
| `trim_whitespace` | `true` | Trim on submit |
| `formats` | `ean_13`, `ean_8`, `code_128`, `code_39`, `upc_a` | Scanner symbology hints |
| `facing_mode` | `environment` | Rear camera by default |
| `form_theme` | `form_div_layout.html.twig` | Must match app Twig form themes |

Per-field options (`container_class`, `input_class`, `button_class`, `autofocus`, `strip_non_printable`) are defined on `BarcodeType` — see `docs/USAGE.md`.

---

## Explicit non-goals

- Barcode generation or printing.
- Server-side symbology checksum validation (application responsibility).
- Inventory lookup or product database integration.
- Demo-only behavior unless documented as stable API.

---

## Validation

| Check | Command |
| --- | --- |
| Full QA | `composer qa` or `make release-check` |
| PHP tests | `vendor/bin/phpunit` |
| TS tests | `make test-ts` |
| Code inventory | `find src -type f \| wc -l` equals inventory total |

When changing behavior, update this spec, `code-inventory.md`, tests, and integrator docs.
