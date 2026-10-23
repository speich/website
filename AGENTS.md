# AGENTS.md

Guidance for AI agents working in this repository (www.speich.net).

## PHP syntax checking

PHP is **not** installed on the host. It is available inside the `frankenphp` docker container.
The project root is mounted at `/app/speich.net` inside the container (host path → `/app/speich.net/...`).

To lint a file, run (use the container path):

```bash
docker exec frankenphp php -l /app/speich.net/<relative/path/to/file>
```

Example:

```bash
docker exec frankenphp php -l /app/speich.net/photo/photodb/scripts/php/PhotoDetail.php
```

To check the PHP version:

```bash
docker exec frankenphp php -v
```

## Project layout

- `photo/photodb/` — the photo database (PHP, SQLite).
  - `scripts/php/PhotoList.php`, `PhotoDetail.php` — page renderers.
  - `scripts/php/Sql*.php` — SQL query builders (extend `SqlExtended`/`SqlFull`).
- `wp/` — WordPress (do not modify).
- `baikal/` — CalDAV/CardDAV server (do not modify).

## Related projects

- The **publish/export routines** that feed the photodb (reading image + thumbnail
  sizes and writing `ImgWidth`, `ImgHeight`, `ThumbWidth`, `ThumbHeight` into the
  `Images` table) live in a **separate project**: `~/Websites/fotodb/` (not in this repo).
