# Security Policy

## Table of contents

- [Supported Versions](#supported-versions)
- [Reporting a Vulnerability](#reporting-a-vulnerability)
- [Scope and attack surface](#scope-and-attack-surface)
- [Threat model and mitigations](#threat-model-and-mitigations)
- [Dependencies and updates](#dependencies-and-updates)
- [Release security checklist (12.4.1)](#release-security-checklist-1241)

## Supported Versions

| Version | Supported          |
| ------- | ------------------ |
| 1.x     | :white_check_mark: |

## Reporting a Vulnerability

We take the security of `BarcodeInputBundle` seriously.

Please report vulnerabilities privately by email: **hectorfranco@nowo.tech**.

Do not open public issues for security-sensitive reports.

## Scope and attack surface

This bundle provides:

- one Symfony form type (`BarcodeType`)
- one data transformer (`BarcodeValueTransformer`)
- Twig form theme templates
- a TypeScript behavior with optional camera barcode scanning (`@zxing/browser`)

There are no HTTP controllers, no API endpoints, no persistence layer, and no cryptographic operations in this bundle.

**CSRF:** This bundle ships form field widgets only. The host application owns the wrapping form CSRF token.

## Threat model and mitigations

- **Input normalization**
  - Barcode values are normalized server-side through `BarcodeValueTransformer`.
  - Non-printable characters can be stripped; whitespace trimmed per configuration.
  - Value length is bounded by configuration (`max_length`, 1–256).
- **Frontend constraints**
  - The browser-side script normalizes input on blur and after camera scans.
  - Frontend checks improve UX only; server-side normalization remains the trust boundary.
- **Camera access**
  - Camera scanning requires explicit user action (scan button) and browser permission.
  - Video stream is used locally for decoding; no barcode data is sent to third-party servers by the bundle.
- **XSS**
  - The bundle does not inject untrusted HTML.
  - Twig templates render standard form inputs and escaped attributes.
- **Authentication/authorization**
  - Not handled by this bundle (must be enforced by the host application where needed).
- **Secrets**
  - No bundle feature requires hardcoded secrets.
  - Repository policy: keep `.env` and local credentials untracked.

## Dependencies and updates

- Run `composer audit` regularly.
- Keep Symfony and PHPUnit/dev tooling updated through normal dependency maintenance.
- Review release notes before upgrading transitive frontend tooling (pnpm/Vite/Vitest) and `@zxing/browser`.

## Release security checklist (12.4.1)

Before tagging a release, confirm:

| Item | Notes |
|------|--------|
| **SECURITY.md** | This document is current and linked from the README where applicable. |
| **`.gitignore` and `.env`** | `.env` and local env files are ignored; no committed secrets. |
| **No secrets in repo** | No API keys, passwords, or tokens in tracked files. |
| **Recipe / Flex** | Default recipe or installer templates do not ship production secrets. |
| **Input / output** | Barcode normalization and length limits preserved; outputs escaped in Twig/templates where user-controlled. |
| **Dependencies** | `composer audit` run; issues triaged. |
| **Logging** | Logs do not print secrets, tokens, or session identifiers unnecessarily. |
| **Cryptography** | If used: keys from secure config; never hardcoded. |
| **Permissions / exposure** | Camera permission is user-initiated; no admin routes in bundle. |
| **Limits / DoS** | `max_length` bounds input size; rate limits are host-app responsibility. |
| **REQ-SEC-004 (AI audit)** | Pass (conditional) — Low residual (CSS class options are developer-trusted; camera access is user-initiated; barcode checksum validation is host-app responsibility); see monorepo `BUNDLES_SECURITY_ANALYSIS.md` (audit **2026-08-27**). |

Record confirmation in the release PR or tag notes.
