# Pull Request Draft

## Title

Add Docker Compose setup and configure Laravel environment for containerized development

## Summary

This PR adds containerization support to the project by introducing a Docker Compose stack for the Laravel application, nginx, MySQL, and n8n.

### What changed

- Added `docker-compose.yml` to define `app`, `nginx`, `db`, and `n8n` services.
- Updated `Dockerfile` to use `php:8.2-fpm` and install required PHP extensions for the project.
- Updated `.env` and `.env.example` to use `DB_HOST=db`, `DB_PORT=3306`, `DB_DATABASE=hutch`, `DB_USERNAME=hutch`, and `DB_PASSWORD=secret`.
- Remapped MySQL host port to `3307` to avoid conflict with local MySQL instances.
- Verified migrations via `docker compose exec -T app php artisan migrate --force`.

## Testing

1. Run `docker compose up -d --build`.
2. Confirm services are running:
    - Laravel: `http://localhost:8080`
    - n8n: `http://localhost:5678`
3. Confirm database is accessible from the app container.
4. Confirm migrations complete successfully.

## Files changed

- `Dockerfile`
- `docker-compose.yml`
- `.env`
- `.env.example`

## Notes

This PR is intended to make local development reproducible with Docker and to provide a ready-to-use `n8n` workflow container.
