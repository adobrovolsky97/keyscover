<?php

namespace App\Console\Commands;

use App\Enums\Config\Key;
use App\Notifications\CrmDisabledNotification;
use App\Services\Config\Contracts\ConfigServiceInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class SendCrmDisabledNotificationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crm-disabled-notification:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notification when CRM sync is disabled';

    /**
     * Execute the console command.
     */
    public function handle(ConfigServiceInterface $configService)
    {
        if ($configService->getValue(Key::IS_CRM_ENABLED->value)) {
            $this->info('CRM sync is enabled');
            return;
        }

        Notification::route('telegram', config('services.telegram-bot-api.recipient'))->notify(new CrmDisabledNotification());
    }
}
