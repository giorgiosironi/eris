# AGENTS.md

Guide for AI agents working on this project.

## Project

Eris is a PHP library for property-based testing, integrating with PHPUnit.

## Repository Layout

- `src/` — library source code (PSR-4 namespace `Eris\`)
- `test/` — unit and end-to-end tests (shares the `Eris\` namespace)
- `examples/` — example test cases demonstrating library usage
- `docs/` — documentation source
- `.docker/` — Dockerfile used by `docker-compose.yml`

## Requirements

- Docker

> **Prefer Docker over the host PHP installation.** All commands below should be run inside a Docker container to avoid dependency on the PHP version installed on the host machine.

## Starting a Docker Shell

Use `make` to spin up a shell inside a container for a specific PHP version:

```bash
make run-php-8.1   # PHP 8.1
make run-php-8.2   # PHP 8.2
make run-php-8.3   # PHP 8.3
make run-php-8.4   # PHP 8.4
```

The project root is mounted at `/usr/src/eris` inside the container. All subsequent commands should be run from that path.

## Setup

```bash
composer install
```

## Running Tests

Run the full test suite:

```bash
vendor/bin/phpunit test
```

Or via Composer:

```bash
composer test
```

Run only a specific test suite (defined in `phpunit.xml`):

```bash
vendor/bin/phpunit --testsuite Full
vendor/bin/phpunit --testsuite Examples
vendor/bin/phpunit --testsuite EndToEnd
```

Run a single test file:

```bash
vendor/bin/phpunit test/SampleTest.php
```

## Static Analysis

```bash
composer static
```

This runs both PHPStan and Psalm.

## Code Style

```bash
composer cs
```

Runs `php-cs-fixer` to fix code style issues.

## Refactoring

```bash
composer rector
```
