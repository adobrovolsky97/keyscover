<?php

namespace App\Jobs;

use App\Mail\ProductAvailableMail;
use App\Models\ProductSubscription\ProductSubscription;
use App\Services\UserNotification\Contracts\UserNotificationServiceInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Log;
use Throwable;

class SendProductAvailableEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public ProductSubscription $productSubscription;

    /**
     * Create a new job instance.
     * @param ProductSubscription $productSubscription
     */
    public function __construct(ProductSubscription $productSubscription)
    {
        $this->productSubscription = $productSubscription;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Mail::to($this->productSubscription->user->email)->send(new ProductAvailableMail($this->productSubscription));
            $this->productSubscription->update(['is_reminder_sent' => true]);

            app(UserNotificationServiceInterface::class)->create([
                'user_ids' => [$this->productSubscription->user_id],
                'text'     => "'{$this->productSubscription->product->name}' знову в наявності."
            ]);
        } catch (Throwable $exception) {
            Log::error($exception->getMessage());
        }
    }
}
