# Installer Setup

## Run Installer

Installer access is allowed when:
- `CI_ENVIRONMENT = development`, or
- `INSTALLER_ENABLED = true` in root `.env`

1. Start your app in browser.
2. Open:

`/install`

3. Complete all 4 installer screens:
- Database setup
- App base URL setup
- Run migration
- Cleanup

The installer will generate root `.env` from `env`, then update:
- `database.default.hostname`
- `database.default.database`
- `database.default.username`
- `database.default.password`
- `app.baseURL`

## Run Installer Again

After completion, installer is locked by:

`writable/installer/installed.lock`

Remove lock file to run installer again:

```bash
rm writable/installer/installed.lock
```

Then open:

`/install`

## Requirements Tasks On Complete Screen

Installer reads tasks from:

`requirements.txt`

On `/install/complete`, it runs commands one by one (one line at a time) and shows output on the screen.

You can rerun tasks from the same complete screen button.
