<?php
/**
 * @author Hojjat koochak zadeh
 */

namespace App\Listeners;

use App\Jobs\SendUserVerifyCode;
use App\Notifications\VerifyCodeGenerated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendVerifyCodeNotification implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $user = $event->user;
        SendUserVerifyCode::dispatch($user);
    }
}
