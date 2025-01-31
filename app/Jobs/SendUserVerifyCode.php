<?php
/**
 * @author Hojjat koochak zadeh
 */

namespace App\Jobs;

use App\Models\User;
use App\Notifications\VerifyCodeGenerated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Notification;

class SendUserVerifyCode implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public User $user
    )
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $code = rand(100000, 999999);
        $this->user->update([
            'verify_code' => $code,
            'verify_code_expire_at' => now()->addSeconds((int) env('VERIFY_CODE_LIFE_TIME', 180))
        ]);
        Notification::send($this->user, new VerifyCodeGenerated($code));
    }
}
