# Release checklist

Use this checklist when cutting a new version. The workflow [.github/workflows/release.yml](../.github/workflows/release.yml) runs on push of a tag `v*` and creates the GitHub Release with body from the tag message and the matching changelog section.

## Before tagging

1. **CHANGELOG.md**
   - Move [Unreleased] entries to a new version section: `## [X.Y.Z] - YYYY-MM-DD` (e.g. `## [1.0.0] - 2026-04-01`).
   - Keep an empty `## [Unreleased]` at the top for future changes.

2. **UPGRADING.md**
   - Add or update upgrade notes for the new version if there are breaking or notable changes.

3. **Run release-check**
   - From the bundle root: `make release-check`.
   - Current chain: `ensure-up`, `composer-sync`, `cs-fix`, `cs-check`, `rector-dry`, `phpstan`, `test-coverage`, `release-check-demos`, `test-ts` (alias `assets-test`).

4. **Optional checks**
   - `make validate-translations` (YAML translation files under `src/Resources/translations/`).
   - `make validate` (Composer metadata).
   - `make update-deps` (refresh Composer lock files in bundle and demos before cutting a maintenance release).

   If `release-check-demos` fails because Docker cannot bind the default demo HTTP port (`8071`), stop the conflicting containers or run the demo with another port, for example:

   ```bash
   (cd demo/symfony8 && PORT=18071 make release-check)
   ```

5. **Commit**
   - Commit `docs/CHANGELOG.md`, `docs/UPGRADING.md` and any other release-related changes.
   - Push to `main` (or merge your release branch).

## Tag and push

Replace `X.Y.Z` with the version (e.g. `1.0.0`):

```bash
git checkout main
git pull origin main
git tag -a vX.Y.Z -m "Release vX.Y.Z"
git push origin vX.Y.Z
```

- Tag format must be **`vX.Y.Z`** (e.g. `v1.0.0`) so the workflow and Packagist recognize it.
- After the push, GitHub Actions creates the release and appends the changelog entry for that version to the release body.
- Packagist will pick up the new tag automatically.

After creating the release commit and tag, run `make check-no-cursor-coauthor` again **before** `git push` (REQ-GIT-001). The release commit itself is not covered by an earlier `release-check` run.

### Example for v1.0.0

After running `make release-check` and committing all changes (CHANGELOG, UPGRADING, docs, and any CS/test fixes):

```bash
git checkout main
git pull origin main
git tag -a v1.0.0 -m "Release v1.0.0"
git push origin v1.0.0
```
