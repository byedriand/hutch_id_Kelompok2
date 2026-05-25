# GitHub Issue Draft

## Title

Dockerize Laravel project with n8n workflow service and fix container environment

## Description

This issue covers containerizing the existing Laravel application and adding an `n8n` workflow automation service.

### Scope

- Create a Docker Compose stack for:
    - Laravel PHP-FPM app
    - Nginx web server
    - MySQL database
    - n8n automation service
- Use PHP 8.2 for compatibility with current Composer lock dependencies.
- Install required PHP extensions and build a working app image.
- Update Laravel environment configuration to connect to the Docker MySQL service.
- Resolve local port conflicts by remapping MySQL host port to `3307`.
- Ensure migrations can run successfully inside the container.

## Acceptance Criteria

- `docker compose up -d --build` starts the full stack without port-binding failure.
- Laravel is accessible at `http://localhost:8080`.
- n8n is accessible at `http://localhost:5678`.
- The database service is reachable from the app container using `DB_HOST=db` and `DB_PORT=3306`.
- A host port of `3307` is used for local MySQL access to avoid conflicts with an existing local MySQL instance.
- `php artisan migrate --force` succeeds in the `app` container.
- `.env.example` is updated to match the Docker stack.

## Changed Files

- `Dockerfile`
- `docker-compose.yml`
- `.env`
- `.env.example`

## Notes

Migration has already been confirmed successfully inside the app container.
