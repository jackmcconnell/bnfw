# 502 errors running BNFW premium add-ons locally on macOS

## Symptom

On a **local macOS** dev site (Local by Flywheel, Valet, MAMP, etc.) with the
premium BNFW add-ons active, admin pages — especially **Plugins**, and the
**Activate / Deactivate licence** actions — return **502 Bad Gateway**, sometimes
on every request.

## This is a macOS environment issue, not a BNFW bug

- The premium add-ons use the Easy Digital Downloads Software Licensing SDK,
  which makes **outbound HTTPS requests** to the store (licence
  activation/validation and update checks).
- On macOS, PHP making an outbound HTTPS request from a forked PHP-FPM worker can
  trip the Objective-C runtime's **fork-safety** check, which aborts the worker
  (`SIGABRT`). nginx then returns a 502.
- It only occurs on **macOS** local PHP-FPM stacks that hit this condition (it
  depends on the PHP build, extensions and macOS version — not every Mac/Local
  setup triggers it). **Production (Linux) hosting is unaffected**, and there is
  no plugin-side fix — the crash is at the C level during the network call, so it
  cannot be caught or prevented from PHP.

## Confirm it's this

Check the site's PHP-FPM log (in Local: `<site>/logs/php/php-fpm.log`) for:

```
child NNN said into stderr: "objc[NNN]: +[NSPlaceholderString initialize] may have
been in progress in another thread when fork() was called. We cannot safely call
it or ignore it in the fork() child process. Crashing instead."
child NNN exited on signal 6 (SIGABRT)
```

The matching nginx error log shows `upstream prematurely closed connection`.

## The fix: `OBJC_DISABLE_INITIALIZE_FORK_SAFETY`

The variable must be in the **PHP-FPM master process's environment at launch** —
the Obj-C runtime reads it once, at startup, and forked workers inherit it.

Things that do **not** work (don't waste time on them):

- `launchctl setenv OBJC_DISABLE_INITIALIZE_FORK_SAFETY YES` — Local launches
  PHP-FPM with a curated environment and ignores it.
- An `env[OBJC_DISABLE_INITIALIZE_FORK_SAFETY]` line in the FPM pool config — it's
  applied inside the worker *after* the fork, which is too late for libobjc.
- Disabling the `imagick` extension.

What works reliably is **wrapping the `php-fpm` binary** so the variable is
exported before it launches. For Local (Apple Silicon path shown — use `darwin`
instead of `darwin-arm64` on Intel, and match your site's PHP version):

```bash
SBIN="$HOME/Library/Application Support/Local/lightning-services/php-8.2.23+0/bin/darwin-arm64/sbin"
cd "$SBIN"
mv php-fpm php-fpm.orig
cat > php-fpm <<'EOF'
#!/bin/sh
export OBJC_DISABLE_INITIALIZE_FORK_SAFETY=YES
exec "$(dirname "$0")/php-fpm.orig" "$@"
EOF
chmod +x php-fpm
```

Then **restart the site** in Local. Confirm `php-fpm.log` shows no new `SIGABRT`
after the restart, and the admin pages load.

### Caveats

- The wrapper is tied to that **PHP version**. If you change the site's PHP
  version, wrap that version's binary too.
- It modifies Local's shared PHP service, so a **Local update may revert it** —
  re-apply if the 502s come back after an update.

### Revert

```bash
cd "$SBIN" && rm php-fpm && mv php-fpm.orig php-fpm
```

## In short

A 502 here means "macOS Obj-C fork-safety crash on an outbound HTTPS request from
the licensing SDK," and only on a **local macOS** dev stack. Apply the wrapper,
restart the site, done. **Live sites on normal (Linux) hosting are never
affected.**
