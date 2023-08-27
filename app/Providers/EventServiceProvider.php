<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Queue\Events\QueueBusy;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    public function boot(): void
    {
        Event::listen(function (QueueBusy $event) {
            // TODO send notification
            info('---------------------BUSY ALERT----------------------', [
                'connection' => $event->connection,
                'queue' => $event->queue,
                'size' => $event->size,
            ]);
//            Notification::route('mail', 'dev@example.com')
//                ->notify(new QueueHasLongWaitTime(
//                    $event->connection,
//                    $event->queue,
//                    $event->size
//                ));
        });
    }

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
