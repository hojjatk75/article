<?php
/**
 * @author Hojjat koochak zadeh
 */

namespace App\Providers;

use App\Events\Auth\UserRegistered;
use App\Listeners\SendVerifyCodeNotification;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    private array $event_listeners = [
        UserRegistered::class => [
            SendVerifyCodeNotification::class
        ]
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        foreach ($this->event_listeners as $event => $listeners) {
            foreach ($listeners as $listener) {
                Event::listen($event, $listener);
            }
        }
    }
}
