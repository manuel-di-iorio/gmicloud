# GMI Cloud - GameMaker Italia Platform

A server-side platform for managing game leaderboards, scores, and player data. Designed for game developers using GameMaker Studio 2, it provides a REST API for score submission and retrieval, player authentication, cloud save synchronization, and team-based game management.

## Tech Stack

| Layer | Technology |
|---|---|
| Language | PHP 8 (vanilla, no framework) |
| Database | MySQL (mysqli, prepared statements) |
| Auth | Discord OAuth2 (developers), separate player auth flow |
| Session | AES-128-CBC encrypted cookies with HMAC-SHA256 signing |
| Frontend CSS | Compiled `assets/css/app.css` (Tailwind 3.4 + Sass design system, light only) |
| Frontend JS | Vanilla JS, Chart.js, Tippy.js, Popper.js |
| i18n | Custom JSON-based system with `__()` helper (EN, IT, ES, FR, DE) |
| API | REST (JSON), rate-limited, hash-verified score submission (SHA-1 + secret) |
| SDK | GameMaker Studio 2 package (GML) |
| Hosting | Altervista / XAMPP, Cloudflare-compatible |

## File Structure

```
/
├── assets/              Static files (compiled CSS, JS, images, UI Kit)
├── assets/design-system/  Design system sources (Tailwind entry + SCSS tokens/atoms/sections)
├── scripts/build-css.mjs  CSS build (Tailwind → Sass → assets/css/app.css, committed)
├── includes/            Master layout and reusable template partials
├── lib/                 Core server libraries (DB, auth, CSRF, i18n, etc.)
├── models/              Database entity classes with schema definitions
├── pages/               View templates organized per page
├── api/                 REST API endpoints (v1 + internal)
├── sdk/                 GameMaker Studio 2 integration package
├── locales/             Translation files (JSON)
├── migrations/          Database migration scripts
├── player-auth/         Player-side OAuth flow
├── .env.example         Environment configuration template
├── .htaccess            Security rules and PHP engine config
└── *.php                Root-level entry points (controllers)
```

## Architecture

The project follows a custom MVC-like pattern:

1. **Root PHP files** act as controllers -- each handles a user action, sets a `$view` variable, and delegates rendering to `includes/layout.php`.
2. **`/pages/`** contains view templates organized in subdirectories. Each page folder holds `.view.php` (HTML template), optional `.script.php` (page-specific JS), and `-tab-render.php` files for multi-tab interfaces.
3. **`/models/`** defines static entity classes with a `$schema` array describing table structure, indexes, foreign keys, and relations.
4. **`/lib/`** provides shared services: database connection, session management, CSRF protection, rate limiting, encryption, i18n, and more.
5. **`/includes/`** contains the master layout and reusable components (navbar, footer, table, filters).
6. **`/assets/ui-kit/`** exposes 16+ reusable PHP UI components (Button, Input, Modal, Table, Tabs, Toast, etc.) each with its own render function (the `ds-*` styles come from the compiled design system).

### Request Flow

```
Browser request --> root PHP file (controller)
                  --> sets $view, $pageName, loads data
                  --> require "includes/layout.php"
                      --> renders full HTML (head, tailwind, navbar, sidebar)
                      --> includes pages/$view/$view.view.php
                      --> includes footer, toast system, tutorial
```

## Environment Configuration

The `.env` file is not tracked in the repository. Copy `.env.example` to `.env` and fill in your values:

```bash
cp .env.example .env
```

Key configuration sections:

- **Database** -- MySQL host, port, name, user, password
- **Discord OAuth2** -- Client ID, client secret, redirect URI for developer login
- **reCAPTCHA** -- Site key and secret for form protection
- **Cookie encryption** -- AES key and HMAC secret for session security
- **Analytics** -- Google Tag Manager ID
- **HTTPS** -- Toggle HTTPS enforcement (set behind Cloudflare)
- **Maintenance** -- Toggle maintenance mode

## .htaccess

The root `.htaccess` handles:

- **Security** -- Blocks direct browser access to `/includes` and `/pages`
- **Caching** -- Sets 1-year cache headers for static assets
- **404** -- Custom error page
- **PHP Engine** -- Routes requests through Altervista's PHP 8 engine

## API

Endpoints live under `/api/`. The public v1 API handles:

| Endpoint | Method | Description |
|---|---|---|
| `/api/v1/scores/add.php` | POST | Submit a score (rate-limited, hash-verified: SHA-1 + secret) |
| `/api/v1/scores/list.php` | GET | Retrieve scores for a game/leaderboard |
| `/api/v1/sync.php` | POST | Cloud save synchronization |
| `/api/v1/gmi-login.php` | GET | Discord OAuth callback for player login |
| `/api/v1/player-logout.php` | POST | Player session logout |

Internal admin endpoints under `/api/internal/` require server-side authentication.

## GameMaker SDK

The `/sdk/` directory contains a GameMaker Studio 2 package (`sdk.yymps`) with GML scripts:

- `gmi_init` -- Initialize SDK with game credentials
- `gmi_scores_send` -- Submit a score
- `gmi_scores_get_list` -- Fetch leaderboard data
- `gmi_login` / `gmi_logout` -- Player authentication
- `gmi_sync_flush` -- Cloud save synchronization
- `gmi_draw_debug` -- Debug overlay for development

Import the `.yymps` file into your GameMaker project via Marketplace > Local Package.

## Database

MySQL tables are defined in `/models/` via schema arrays and managed through numbered migration scripts in `/migrations/`. Run the migration endpoint to apply pending schema changes.

Core tables: `users`, `games`, `players`, `scores`, `bans`, `leaderboards`, `teams`, `team_members`, `cloud_saves`, `achievements`, `player_achievements`, `api_errors`, `sync_operations`.

## Internationalization

Translations are stored as JSON files in `/locales/`. The `getLang.php` library reads the locale from the user's cookie or browser preference and loads the corresponding file. Use the `__()` helper function to retrieve translated strings throughout the codebase.

Supported languages: English, Italian, Spanish, French, German.

## Design System

The UI follows the GMI Frontend portal design system (light only, square corners, no shadows):

- **Tokens**: `assets/design-system/scss/tokens.scss` (`--ds-*` variables, page `#E8EDF1`, surface `#FFFFFF`)
- **Atoms**: `assets/design-system/scss/atoms/` — one `ds-*` class per component (`ds-btn`, `ds-alert`, `ds-badge`, `ds-card`, `ds-input`, `ds-table`, `ds-tabs`, `ds-modal`, `ds-toast`, …)
- **PHP API unchanged**: `ui_button()`, `ui_alert()`, etc. keep their signatures; only the emitted HTML/CSS changed
- **Preview**: open `/ui-kit.php` in the browser to see every atom rendered

Rebuild the CSS after touching the design system (requires Node 20+):

```bash
npm ci
npm run build:css   # writes assets/css/app.css (committed: the deploy is git-ftp)
npm run watch:css   # rebuild on change (dev)
```

CI verifies reproducibility (`npm ci && npm run build:css` must not dirty `assets/css/app.css`).

## Development

1. Ensure PHP 8+ and MySQL are available (XAMPP or similar)
2. Clone the repository
3. Copy `.env.example` to `.env` and configure
4. Import the database schema or run migrations
5. Access the site via `http://localhost/htdocs/` (XAMPP) or the Altervista URL
