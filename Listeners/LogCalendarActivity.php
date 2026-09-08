<?php

declare(strict_types=1);

namespace Modules\Calendar\Listeners;

use Modules\Calendar\Models\Calendar;
use Spine\Calendar\EntityCreated;
use Spine\Calendar\EntityDeleted;
use Spine\Calendar\EntityUpdated;
use Spine\Services\ActivityLogService;

/**
 * HOOK — entity lifecycle generic (HasLifecycleHooks) untuk Calendar.
 * created/updated/deleted -> activity log.
 */
class LogCalendarActivity
{
    public function __construct(private readonly ActivityLogService $activityLog)
    {
    }

    public function created(EntityCreated $event): void
    {
        if (! $event->entity instanceof Calendar) {
            return;
        }

        $this->activityLog->log(
            "Calendar created: " . $this->label($event->entity),
            $event->entity,
            $this->user(),
            ['event' => 'created'],
        );
    }

    public function updated(EntityUpdated $event): void
    {
        if (! $event->entity instanceof Calendar) {
            return;
        }

        $changes = $event->changes;

        $this->activityLog->log(
            "Calendar updated: " . $this->label($event->entity) . " (" . $this->describe($changes) . ")",
            $event->entity,
            $this->user(),
            ['event' => 'updated', 'changes' => $changes],
        );
    }

    public function deleted(EntityDeleted $event): void
    {
        if (! $event->entity instanceof Calendar) {
            return;
        }

        $this->activityLog->log(
            "Calendar deleted: " . $this->label($event->entity),
            null,
            $this->user(),
            ['event' => 'deleted', 'id' => $event->entity->getKey()],
            null,
            $event->entityType,
        );
    }

    private function describe(array $changes): string
    {
        $parts = [];

        foreach ($changes as $field => $change) {
            if (in_array($field, ['updated_at', 'remember_token'], true)) {
                continue;
            }

            $label = Calendar::labels()[$field] ?? $field;
            $parts[] = $label . ': ' . $change['old'] . ' -> ' . $change['new'];
        }

        return implode(', ', $parts);
    }

    private function label($entity): string
    {
        return (string) ($entity->title ?? $entity->getKey());
    }

    private function user(): ?\Illuminate\Contracts\Auth\Authenticatable
    {
        return auth('sanctum')->user() ?? auth()->user();
    }
}