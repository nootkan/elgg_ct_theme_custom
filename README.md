# CT Theme Custom

A theming and branding plugin for [Elgg](https://elgg.org) 6.x. It provides site-wide styling, custom logo uploads, and admin-editable branding across validation emails, system emails, the registration page, and the walled garden login page.

## Features

### Styling
- Custom CSS for the public site (`css/ct_theme_custom.css`) and the admin area (`css/ct_admin_custom.css`), including topbar color, dropdown menus, header/footer styling, and mobile-responsive fixes for the walled garden login page.

### Logos
- **Website logo** — uploadable via plugin settings, displayed in the site header (`logo.php`) and in the admin topbar (`admin_logo.php`, admin pages only).
- **Email logo** — a separate uploadable logo used in outgoing emails.
- Both logo uploads are validated server-side (real image content via `getimagesize()`, allowed extensions/MIME types, 5MB max size) and stored as `ElggFile` entities with public access, referenced by GUID in plugin settings.

### Email validation (registration confirmation email)
- Admin-editable subject and body templates with placeholder support (`{display_name}`, `{site_name}`, `{validation_link}`, `{logo_url}`).
- Falls back to a bundled default logo and default templates if the admin hasn't customized them.
- Implemented via `ValidateEmailHandler.php`, which overrides Elgg core's validation email handler.
- `EmailValidationHandler.php` and `EmailValidator.php` contain earlier/alternate implementations of similar logic and may be candidates for cleanup (see below).

### Global system email branding
- A "Use logo in all system emails" setting applies the email logo and a custom site name to **other** system emails (e.g. password reset), excluding the validation email (which has its own template).
- Implemented via `GlobalEmailHandler.php`, hooked into Elgg's central notification pipeline through a plugin bootstrap class (`Bootstrap.php`).

### Registration page
- Admin-editable notice (HTML allowed) shown above the registration form, with configurable CSS classes and inline styles. Defaults to a spam-folder reminder if left blank.

### Walled Garden login page
- Admin-editable introduction text shown above the username field, restricted to the actual login routes (`index`, `account:login`) so it doesn't leak into other reused instances of the login form view.

### Settings
- All settings are managed through a single custom `settings.php` admin view and saved via a custom `save.php` action, which also handles the two logo uploads/removals. Only an explicit allow-list of setting keys can be written, and two admin-HTML fields (`registration_message`, `email_body`, `login_intro_message`) are passed through Elgg's sanitizer as defense-in-depth.

## File overview

| File | Purpose |
|---|---|
| `elgg-plugin.php` | Plugin manifest: settings defaults, action/view registrations, bootstrap registration. |
| `Bootstrap.php` | Registers the `GlobalEmailHandler` against Elgg's notification event system on plugin boot. |
| `save.php` | Settings-save action; handles text settings and both logo uploads/removals. |
| `settings.php` | Admin settings form view. |
| `ValidateEmailHandler.php` | Overrides Elgg core's validation-email subject/body generation. |
| `EmailValidationHandler.php`, `EmailValidator.php` | Earlier/alternate validation email logic (see Known Issues). |
| `GlobalEmailHandler.php` | Injects logo + site name into non-validation system emails. |
| `logo.php`, `admin_logo.php` | Output views for the site header and admin topbar logos. |
| `login_intro.php` | Walled Garden login intro text view. |
| `register.php` | Registration form, extended with the custom registration notice. |
| `salutation.php`, `sign-off.php` | Overrides Elgg's default email salutation/sign-off, skipped for validation emails since those have their own greeting/sign-off baked into the template. |
| `en.php` | Language strings, including the default validation email subject/body fallback. |
| `ct_theme_custom.css`, `ct_admin_custom.css`, `walled_garden.css` | Site, admin, and walled-garden stylesheets. |

## Development history

### Global email logo feature was inactive for an unknown period
The plugin's `Bootstrap.php` class registers the hook that makes the global email logo/site-name feature work, but `elgg-plugin.php` was missing the top-level `'bootstrap' => Bootstrap::class` key required for Elgg to actually call `Bootstrap::boot()`. As a result, the feature had silently never been active, despite appearing to work in earlier informal testing — that testing had actually been observing the validation email's logo, which is generated through a separate, independent code path (`ValidateEmailHandler.php`) and was unaffected by the missing bootstrap registration.

Fixing this surfaced two further issues, uncovered through live debugging:

1. **DI container / hook API mismatch.** `Bootstrap::boot()` originally called `elgg()->plugin_hooks->registerHandler(...)`, which doesn't exist in this Elgg version. The correct call, per Elgg's own bootstrap documentation, is `$this->elgg()->events->registerHandler(...)` — Elgg 6.x has merged its historical "plugin hooks" and "events" systems under a single Events service.

2. **`\Elgg\Hook` vs `\Elgg\Event`.** `GlobalEmailHandler::prepareEmailNotification()` type-hinted its parameter as `\Elgg\Hook`, but the merged Events system passes an `\Elgg\Event` instance instead. Updating the type hint resolved a fatal `TypeError`.

3. **Notification method not available via `$notification->method`.** Once the hook was reliably firing, the logo still wasn't appearing. Diagnostic logging confirmed the handler was running, but the `method` property was never set on the `\Elgg\Notifications\Notification` object at the `'prepare', 'notification'` stage. Per Elgg's own documentation examples, the delivery method must be retrieved via `$hook->getParam('method')` instead. Fixing this was the final change needed to get the logo and site name appearing correctly in system emails like password resets.

### Known issue: not all outgoing email is covered
The global email logo/site-name feature only affects email that passes through Elgg's central Notifications system (i.e., anything that triggers the `'prepare', 'notification'` event). Plugins that send email through other means (e.g. a custom mailer call rather than `notify_user()`) will not pick up the logo/site name automatically — this was observed with a separate `membership_fees` plugin's renewal-reminder email. Extending coverage to such plugins requires either routing their email through Elgg's Notifications system, or adding a small explicit call into the same logo/site-name logic from within that plugin.

### Known issue: redundant/overlapping validation email handlers
`ValidateEmailHandler.php`, `EmailValidationHandler.php`, and `EmailValidator.php` all contain closely related logic for generating the validation email subject/body. Only `ValidateEmailHandler.php` is currently wired up in `elgg-plugin.php`'s `notifications` array. The other two appear to be earlier iterations and may be safe to remove after confirming they're not referenced elsewhere — not yet investigated or cleaned up.

## Requirements
- Elgg 6.x (developed and tested against 6.3.1)
- PHP with the GD or equivalent extension for `getimagesize()` support

## License
GPL-2.0-only (per `composer.json`)
