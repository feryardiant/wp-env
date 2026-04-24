# Agent Guidelines & Project Context

This file serves as a persistent context for AI agents working in this project. All agents MUST read and adhere to these guidelines to ensure consistency and prevent environment breakage.

## 🏗 Project Identity

A Dockerized WordPress local development environment using Apache, MySQL 8.0, and automated initialization via WP-CLI. Includes specialized evaluation tools like **Entry Manager for Contact Form 7**.

## 🛠 Operational Mandates

1.  **Environment Variables**: The project is strictly dependent on a `.env` file. NEVER hardcode values in `compose.yaml`. Verify required variables (see `README.md`) before proposing changes.
2.  **Service Lifecycle**: Always use `docker compose` for starting/stopping services.
3.  **WP-CLI Management**: Use the dedicated `cli` service for all WordPress commands.
    *   Command pattern: `docker compose run --rm cli wp <command>`
4.  **Volumes & Persistence**: Database data is in `volumes/mysql`, and site files are in `volumes/wordpress`. Modification of site files MUST be done with awareness of file permissions (the environment uses user `33` / `www-data`).
5.  **Metadata Management**: ALL AI-generated metadata (plans, specs, and design documents) MUST be stored exclusively in the `.gemini/` directory (e.g., `.gemini/plans/`, `.gemini/specs/`). Do not use any other directory for persistent or temporary agent artifacts.

## 📁 Development Guidelines

1.  **Themes/Plugins**: Prefer creating custom themes/plugins in `packages/` and symlinking them or mounting them into `volumes/wordpress/wp-content/` if intended for portability.
2.  **Initialization**: Changes to site titles, admin users, or pre-installed plugins should be implemented in `docker/init-wp.sh`.
3.  **Security**: NEVER commit the `.env` file or any other secrets to version control.
4.  **Testing Standards**:
    - **Naming**: Use `camelCase` for all test methods (e.g., `testMethodName`). Do NOT use `snake_case`.
    - **Docblocks**: All methods in test classes (including `setUp`, `tearDown`, etc.) MUST include a descriptive docblock and the `@return void` tag.
    - **Assertions**: When mocking hooks with Brain Monkey, ensure `addToAssertionCount()` accurately reflects the number of expectations set.

## 📝 Persistent Memory (Context)

- **Date**: 2026-04-23
- **Status**: Environment is fully documented. Test architecture has been updated to use `camelCase` naming conventions and strict docblock standards (including `@return void`).
- **Next Steps**: Continue maintaining strict PSR-12 and WordPress coding standards across all packages and tests.
