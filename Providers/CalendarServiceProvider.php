<?php

declare(strict_types=1);

namespace Modules\Calendar\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Modules\Calendar\Listeners\LogCalendarActivity;

class CalendarServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../Http/routes/api.php');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // HOOK — entity lifecycle generic (HasLifecycleHooks):
        // EntityCreated/Updated/Deleted untuk Calendar (entity modul ini).
        Event::listen(\Spine\Calendar\EntityCreated::class, LogCalendarActivity::class . '@created');
        Event::listen(\Spine\Calendar\EntityUpdated::class, LogCalendarActivity::class . '@updated');
        Event::listen(\Spine\Calendar\EntityDeleted::class, LogCalendarActivity::class . '@deleted');
    }
}
