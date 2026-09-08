# spine-calendar

Calendar (events) module for the [Spine](https://github.com/laravelspine)
framework — a Laravel module (nwidart/laravel-modules) exposing per-user
calendar events as a REST API, with lifecycle activity logging via Spine's
generic entity hooks.

Package name: `laravelspine/events-module` — namespace `Modules\Events`.

## Features

- Per-user events (each user only sees and manages their own events)
- Date-range filter (`?start=` & `?end=`) for fullcalendar-style range fetches
- Per-event activity log exposed as a REST sub-resource
  (`GET /api/v1/events/{id}/activity-logs`)
- Automatic activity log on create / update / delete via Spine's
  `HasLifecycleHooks` + `EntityCreated/EntityUpdated/EntityDeleted` events
- Registers the `calendar` widget on the Spine dashboard (`left-8` area),
  backed by this module's API

## Requirements

- PHP ^8.3
- Laravel + [nwidart/laravel-modules](https://github.com/nwidart/laravel-modules) ^12.0
- Spine core (provides `HasLifecycleHooks`, `ActivityLogService`, and the
  `auth:sanctum` authentication)

## Installation

```bash
composer require laravelspine/events-module
php artisan migrate
```

The service provider is auto-discovered from `composer.json`. The module also
ships a `manifest.php` that declares its dashboard widget, which the Spine
frontend consumes.

## API

All endpoints require authentication (`auth:sanctum`) and operate on the
authenticated user's events only.

| Method | Endpoint                          | Description                      |
|--------|-----------------------------------|----------------------------------|
| GET    | `/api/v1/events`                  | List current user's events       |
| POST   | `/api/v1/events`                  | Create an event                  |
| GET    | `/api/v1/events/{id}`             | Show one event                   |
| PUT    | `/api/v1/events/{id}`             | Update an event                  |
| DELETE | `/api/v1/events/{id}`             | Delete an event                  |
| GET    | `/api/v1/events/{id}/activity-logs` | Activity log for one event     |

### Range filter

`GET /api/v1/events?start=2026-09-01&end=2026-09-30` — events whose `start`
falls within the range, ordered by `start` ascending.

### Payload

```json
{
  "title": "Team standup",
  "description": "Weekly sync",
  "start": "2026-09-08T09:00:00",
  "end": "2026-09-08T09:30:00",
  "color": "#6c757d"
}
```

- `title` — required on create, string, max 190
- `start` — required on create, valid date
- `end` — optional, must be after or equal to `start`
- `color` — optional, string, max 20 (e.g. a hex color for the calendar)
- `user_id` is set server-side from the authenticated user

## Data model

`events` — see `database/migrations/`:

| Column      | Type            | Notes                          |
|-------------|-----------------|--------------------------------|
| id          | bigint (PK)     |                                |
| ulid        | string, unique  | public identifier              |
| title       | string          |                                |
| description | text, null      |                                |
| start       | datetime        |                                |
| end         | datetime, null  |                                |
| color       | string(20), null|                                |
| user_id     | bigint, indexed | owner                          |
| timestamps  |                 |                                |

## Activity log

The `LogEventActivity` listener writes one entry per lifecycle event to the
Spine activity log (`activity_logs`), including the diff (`changes`) on
updates. Read them back per event via
`GET /api/v1/events/{id}/activity-logs`.

## License

MIT — see [LICENSE](LICENSE).
