# Contributing

Contributions are welcome. Please open an issue or a pull request on [GitHub](https://github.com/nowo-tech/OtpInputBundle).

- Follow the code style (PHP-CS-Fixer, see root `.php-cs-fixer.dist.php`).
- Run `make cs-check`, `make rector-dry`, `make phpstan`, `make igor`, and `make test` (or `make release-check`) before submitting.

## Table of contents

- [Code of Conduct](#code-of-conduct)
- [Git hooks (REQ-GIT-001)](#git-hooks-req-git-001)
- [Cursor Agent (REQ-IDE-006)](#cursor-agent-req-ide-006)

## Code of Conduct

This project follows the [Contributor Covenant Code of Conduct](../CODE_OF_CONDUCT.md). By participating, you are expected to uphold it. Please report unacceptable behavior to **hectorfranco@nowo.tech**.

## Git hooks (REQ-GIT-001)

Do **not** add `Co-authored-by: Cursor` or `cursoragent@cursor.com` trailers, nor `Co-Authored-By: Claude …` / `noreply@anthropic.com` ones, to commit messages.

```bash
# Playwright e2e + README widget screenshots (REQ-DEMO-013)
make -C demo/symfony8 test-e2e
make -C demo/symfony8 demo-screenshots
make setup-hooks
make check-no-cursor-coauthor
```

`make setup-hooks` installs `.githooks/commit-msg` (or sets `core.hooksPath` to `.githooks`). Run it once per clone before your first commit.
If CI fails because trailers are already on the remote, see [GITHUB_CI.md](GITHUB_CI.md) (REQ-GIT-001) and run `make strip-cursor-coauthor-from-history` before `git push --force-with-lease`.

## Cursor Agent (REQ-IDE-006)

Contributors using Cursor Agent on this repository should:

1. **Submit with Ctrl+Enter** (macOS: **⌘+Enter**). **Enter** inserts a newline; **Ctrl+Enter** / **⌘+Enter** submits the prompt. UI: **Cursor Settings → Agents / Chat → Submit with Ctrl + Enter**.
2. **Queue follow-ups** while the agent is running. In user `settings.json` (not a repo file):

```json
{
  "cursor.composer.queueMessageDefaultBehavior": "queue"
}
```

Do **not** use `"steer"` as the org default: steer interrupts the running agent as soon as a follow-up is sent.
