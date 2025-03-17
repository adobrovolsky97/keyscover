<?php

namespace App\Jobs;

use App\Enums\Config\Key;
use App\Mail\WelcomeMail;
use App\Models\User\User;
use App\Services\Config\Contracts\ConfigServiceInterface;
use App\Services\UserNotification\Contracts\UserNotificationServiceInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendWelcomeNotificationsForUserJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public User $user;

    /**
     * Create a new job instance.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $welcomeConfigText = app(ConfigServiceInterface::class)->getValue(Key::WELCOME_MESSAGE->value);
        } catch (Throwable) {
            $welcomeConfigText = null;
        }

        if (empty($welcomeConfigText)) {
            return;
        }

        try {
            Mail::to($this->user->email)->send(new WelcomeMail($this->user, $welcomeConfigText));

            app(UserNotificationServiceInterface::class)->create([
                'user_ids' => [$this->user->id],
                'text'     => $welcomeConfigText
            ]);
        } catch (Throwable $exception) {
            Log::error($exception->getMessage());
        }
    }
}
