# FrankenPHP worker mode audit (kernel not reset between requests)

| Field | Value |
|-------|-------|
| Package | `nowo-tech/barcode-input-bundle` (`symfony-bundle`) |
| Audited revision | `v1.0.0` / `8780fc6` (+ hardening 2026-09-24) |
| Audit date | 2026-09-24 (re-audit; first pass 2026-09-23) |
| Target runtime | FrankenPHP **worker** with **`FRANKENPHP_RESET_KERNEL=false`** (sticky Kernel / “Friendly Worker”) |
| Method | Manual review of every file under `src/` (form type, data transformer, enum, DI extension, configuration, compiler pass, `services.yaml`, Twig themes) + PHPStan classic + worker-strict |
| **Verdict** | ✅ **100% compatible** under Scenario B (`reset_kernel: false`) — no remediations required for sticky Kernel |

## Execution model assumed

FrankenPHP worker mode boots the Symfony kernel once per worker and serves many requests with the same container. This audit assumes the **strict** host contract used by Nowo “Friendly Worker” / kernel-isolation E2E:

| Host flag | Meaning |
|-----------|---------|
| `FRANKENPHP_MODE=worker` | Worker keeps the app in memory |
| **`FRANKENPHP_RESET_KERNEL=false`** | Kernel is **not** rebooted; Scenario **B** below |
| `FRANKENPHP_WORKER_NUM=1` | Single worker (isolation tests) |

Two scenarios are evaluated:

- **A — kernel not rebooted, `services_resetter` still runs:** services tagged `kernel.reset` (or implementing `ResetInterface`) are reset between requests. Typical default when `FRANKENPHP_RESET_KERNEL` is truthy / Runtime `worker=2`-style reset.
- **B — no reset at all (`FRANKENPHP_RESET_KERNEL=false`):** nothing is reset; any per-request state kept in a shared service leaks into the next request.

A bundle that is safe under **B** is safe under **A** and under classic mode / PHP-FPM.

## Summary

| Area | Status | Notes |
|------|--------|-------|
| Mutable state in shared services | ✅ | `BarcodeType` only has `readonly` constructor defaults from container parameters |
| Static properties / `static` locals | ✅ | `BarcodeFormat` only has pure static methods; no static properties |
| `ResetInterface` / `kernel.reset` coverage | ✅ N/A | Nothing to reset |
| Request / user / locale captured in services | ✅ | None; the form type only reads its resolved options |
| Superglobals, `$_ENV`, `putenv`, `ini_set`, `setlocale`, timezone | ✅ | None used; config is compiled into container parameters |
| Doctrine / EntityManager | ✅ N/A | No persistence |
| Output, headers, `exit`, shutdown functions | ✅ | None |
| Resources (files, sockets, cURL) held open | ✅ | None |
| Memory growth across requests | ✅ | No caches or accumulating arrays |
| Blocking I/O and timeouts | ✅ N/A | No I/O; camera scanning runs in the browser |
| Third-party static state | ✅ | Only Symfony Form / DI / Config |
| Array default aliasing on shared form type | ✅ | `formats` defaults are copied (`[...$this->defaultFormats]`) so OptionsResolver never shares a mutable array with the service |
| PHPStan FrankenPHP rulesets | ✅ | `ruleset-classic.neon` + `ruleset-worker-strict.neon` (includes worker) in `phpstan.neon.dist` |

## Services reviewed

| Service | Shared | Mutable state | Scenario A | Scenario B |
|---------|--------|---------------|------------|------------|
| `Nowo\BarcodeInputBundle\Form\BarcodeType` (`form.type`) | yes | none (`readonly` defaults, `src/Form/BarcodeType.php`) | ✅ | ✅ |

`BarcodeValueTransformer` is created with `new` inside `BarcodeType::buildForm()` for each form build, so each form gets its own `readonly` instance; it is never stored in a service. `BarcodeFormat` is a backed enum. `NowoBarcodeInputExtension`, `Configuration` and `TwigPathsPass` only run at container compile time. Twig themes only read `FormView` vars (per request).

## Findings

No open findings.

`buildView()` writes only to the per-request `FormView`, and `configureOptions()` reads immutable constructor values. Under Scenario B the same `BarcodeType` instance is reused; consecutive form builds with different `formats` / options cannot poison later requests (covered by unit regression `testSharedInstanceDoesNotLeakOptionsAcrossConsecutiveBuilds`).

### Closed / hardening notes (2026-09-24)

| ID | Topic | Action |
|----|--------|--------|
| H-01 | Shared array default for `formats` could be aliased into OptionsResolver | Defensive copy in `configureOptions()` — belt-and-suspenders for Scenario B |
| H-02 | PHPStan worker hygiene | Use `ruleset-worker-strict.neon` instead of plain worker (flags request-superglobal access; none present) |

## Usage recommendations in worker mode

- No special configuration or reset hook is needed for this bundle when `FRANKENPHP_RESET_KERNEL=false`.
- Applications that extend or decorate `BarcodeType`, or add a custom transformer, must not keep submitted values in service properties (or must implement `ResetInterface`) to keep this verdict.
- Keeping Symfony’s `services_resetter` enabled in the **application** remains recommended for framework-owned state (Doctrine identity map, security token storage, etc.); this bundle does not depend on it.
- The demo (`demo/symfony8/docker/frankenphp/Caddyfile`) already runs FrankenPHP in worker mode with `watch`.

## Re-audit triggers

Re-run this audit when a change adds: properties to `BarcodeType`, a new service (validator, Twig extension, event listener), a cache of scanned values, a server-side barcode decoding or lookup call (I/O), or any use of `$_SERVER` / `$_ENV` at runtime.
