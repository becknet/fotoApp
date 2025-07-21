# Tech Stack

- PHP 8.3 (FPM)
- MySQL 8 (utf8mb4)
- HTML5 + Bootstrap 5.3
- Vanilla JavaScript (ES6+, Fetch API)
- GD for image processing (fallback: Imagick)

# Project Rules

- MVC folders: `app/Controllers`, `app/Models`, `app/Views`.
- PSR‑12 style; run `composer format` before commits.
- `strict_types=1` at top of every PHP file.
- Only `public/` is web‑root.

# Security Requirements

- Escape output with `htmlspecialchars()`.
- Use PDO prepared statements *only*.
- Regenerate session ID on login; cookies `Secure; HttpOnly; SameSite=Lax`.
- CSRF token on every state‑changing POST.
- Passwords via `password_hash()` (bcrypt, cost 12).

# Database

- Connect as least-privilege user `app_user`.
- Store **file paths**, never binary blobs (`uploads/YYYY/MM/uuid.ext`).

## Tables

### `users`

| column          | type            | constraints                | notes             |
| --------------- | --------------- | -------------------------- | ----------------- |
| `id`            | BIGINT UNSIGNED | PK, AUTO\_INCREMENT        |                   |
| `name`          | VARCHAR(60)     | NOT NULL                   | Display name      |
| `email`         | VARCHAR(255)    | NOT NULL, UNIQUE           | Login identifier  |
| `password_hash` | CHAR(60)        | NOT NULL                   | bcrypt            |
| `created_at`    | TIMESTAMP       | DEFAULT CURRENT\_TIMESTAMP | Registration date |

### `photos`

| column        | type            | constraints                        | notes                       |
| ------------- | --------------- | ---------------------------------- | --------------------------- |
| `id`          | BIGINT UNSIGNED | PK, AUTO\_INCREMENT                |                             |
| `user_id`     | BIGINT UNSIGNED | FK → `users.id`, ON DELETE CASCADE | Owner of the photo          |
| `title`       | VARCHAR(140)    | NOT NULL                           | Picture title               |
| `description` | TEXT            | NULL                               | Optional                    |
| `location`    | VARCHAR(140)    | NULL                               | Where the shot was taken    |
| `file_path`   | VARCHAR(255)    | NOT NULL                           | Path to original image      |
| `thumb_path`  | VARCHAR(255)    | NOT NULL                           | Path to generated thumbnail |
| `created_at`  | TIMESTAMP       | DEFAULT CURRENT\_TIMESTAMP         | Upload date                 |

```sql
-- Example DDL (MySQL 8)
CREATE TABLE users (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(60) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  password_hash CHAR(60) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE photos (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  user_id BIGINT UNSIGNED NOT NULL,
  title VARCHAR(140) NOT NULL,
  description TEXT NULL,
  location VARCHAR(140) NULL,
  file_path VARCHAR(255) NOT NULL,
  thumb_path VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_photos_users FOREIGN KEY (user_id)
    REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

# Supported User Stories

- Upload photo ≤ 10 MB with title, description, location.
- Auto‑create 300 px thumbnail after upload.
- Everyone can view all photos; only *owner* may edit/delete.

# Commands

- `make dev` – start local Docker stack.
- `composer install` – install PHP deps.
- `make test` – run PHPUnit + PHP‑Stan level 8.
- `php artisan migrate` – run DB migrations.

# Project Structure

```
/app
  /Controllers   ← request handlers
  /Models        ← DB access layer
  /Views         ← Blade‑like templates
/config
/public          ← web‑root (CSS, JS, index.php)
/uploads         ← user‑generated images & thumbs
/tests
CLAUDE.md
README.md
docker-compose.yml
```

# Do Not Touch

- `/database/migrations/**` outside migration PRs.
- `main` branch – open PRs against `develop`.
- Secrets – store in `.env` only.

# C‑Criteria Mapping

- **C4** HTML validates via `make lint-html`.
- **C5–C6** client & server validation for every input.
- **C7, C19** XSS & SQLi prevented per *Security Requirements*.
- **C8–C12** session handling & DB permissions as specified.
- **C13–C18** owner‑only edits enforced in `PhotoPolicy.php`.

# Glossary

- **Owner** – user who created a photo (`photos.user_id`).
- **Public Gallery** – `/photos` route; no auth required.

