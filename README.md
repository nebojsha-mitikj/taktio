# Tasks ✅

A productivity app for people who actually want to get things done — not just organize them.

Built with a focus on simplicity: no bloat, no clutter. Just your tasks, your priorities, and a clear picture of what's coming up.

## What it does

**Tasks** — Create tasks with priorities and labels. View what's on your plate today, what's coming up, and what you've already crushed in the history view.

**Recurring templates** — Set up tasks that repeat on a schedule: daily, weekdays only, weekends, or specific days of the week. They show up automatically without any manual effort.

**Smart task generation** — Recurring tasks are generated on-the-fly and only written to the database when you actually interact with them (e.g. marking them done). No thousands of pre-generated rows cluttering your database.

**Labels** — Tag tasks and recurring templates however you like. Filter by label to focus on what matters.

**Monthly plans** — Each month gets a main goal and a list of sub-goals, each with their own steps. Check off steps and the parent goal tracks completion automatically.

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 12 |
| Frontend bridge | Inertia.js |
| Frontend | Vue 3 + TypeScript |
| Styling | Tailwind CSS v4 |
| Build | Vite |
| Database | MySQL (AWS RDS) |

## Getting started

```bash
composer run setup
```

Then visit the app in your browser. That's it.

## Development

```bash
composer run dev       # Starts Laravel, queue worker, log watcher, and Vite
composer run dev:ssr   # Same, with SSR support
```

## Testing

Tests use an in-memory SQLite database so they're fast and self-contained.

```bash
composer run test                                     # Run the full suite
php artisan test --filter=TestClassName               # Run a single test class
php artisan test --filter=TestClassName::test_name    # Run a single test
```

## Linting & Formatting

```bash
vendor/bin/pint          # Fix PHP code style
npm run lint             # Lint Vue/TS with ESLint
npm run format           # Format Vue/TS with Prettier
npm run format:check     # Check formatting without changing anything
```

## Building for production

```bash
npm run build            # Standard production build
npm run build:ssr        # Production build with SSR support
```
