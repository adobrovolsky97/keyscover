<?php

namespace App\Console\Commands;

use App\Models\Order\Order;
use App\Services\Crm\Contracts\CrmServiceInterface;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncOrderStatusesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order-statuses:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync order statuses command';

    /**
     * Execute the console command.
     */
    public function handle(CrmServiceInterface $crmService): void
    {
        $minOrderDate = Order::query()
            ->whereNull('crm_status')
            ->orWhere('crm_status', '!=', Order::STATUS_FINISHED)
            ->min('created_at');
        $maxOrderDate = Carbon::tomorrow();

        if(Carbon::parse($minOrderDate)->diffInDays($maxOrderDate) >= 14) {
            $minOrderDate = Carbon::today()->subDays(14);
        }

        $page = 1;

        do {
            $this->info("Fetching page: $page");
            $response = $crmService->getOrders([
                'q[ordered_at_gteq]' => Carbon::parse($minOrderDate)->format('Y-m-d'),
                'q[ordered_at_lteq]' => Carbon::parse($maxOrderDate)->format('Y-m-d'),
            ], $page);

            $updateData = [];

            foreach ($response['items'] ?? [] as $orderData) {
                $crmStatus = $this->getCrmStatus($orderData['stage']['name']);
                $updateData[] = [
                    'id'                 => $orderData['id'],
                    'crm_status'         => $crmStatus,
                    'is_paid'            => $this->getIsPaid($crmStatus),
                    'nova_poshta_status' => $orderData['deliveries'][0]['status_description'] ?? null,
                    'ttn'                => $orderData['deliveries'][0]['ttn'] ?? null,
                ];
            }

            $this->updateOrders($updateData);
            $lastPage = $response['pagination']['total_pages'] ?? $page;
            $this->info("Page $page processed");
            $page++;
            sleep(2);
        } while ($page <= $lastPage);
    }

    protected function getIsPaid(string $crmStatus): bool
    {
        if (in_array($crmStatus, ['Необроблені', 'Комплектація'])) {
            return false;
        }

        return true;
    }

    protected function updateOrders(array $data): void
    {
        if (empty($data)) {
            return;
        }

        $columns = ['crm_status', 'is_paid', 'nova_poshta_status', 'ttn']; // Columns to update
        $query = "UPDATE `orders` SET ";
        $cases = [];
        $ids = [];

        foreach ($columns as $column) {
            $caseParts = ["`$column` = CASE"];
            foreach ($data as $value) {
                if (array_key_exists($column, $value)) { // Ensure the column exists in input
                    $caseParts[] = "WHEN `crm_order_id` = ? THEN ?";
                    $ids[] = (int)$value['id']; // Ensure correct ID format as integer
                    $ids[] = $value[$column] ?? null; // Handle NULL values
                }
            }
            $caseParts[] = "ELSE `$column` END";
            $cases[] = implode(" ", $caseParts);
        }

        $query .= implode(", ", $cases);
        $query .= " WHERE `crm_order_id` IN (" . implode(',', array_fill(0, count($data), '?')) . ")";

        // Make sure `id` values are integers in the final list of `crm_order_id` for `IN` clause
        foreach ($data as $value) {
            $ids[] = (int)$value['id']; // Ensure correct ID format as integer
        }

        $updatedRows = DB::update($query, $ids);

        $this->warn("Successfully updated $updatedRows rows.");
    }

    protected function getCrmStatus(?string $crmStatus): string
    {
        if ($crmStatus === Order::STATUS_RESERVED) {
            return Order::STATUS_PACKING;
        }

        if ($crmStatus === Order::STATUS_FISCALIZED) {
            return Order::STATUS_FINISHED;
        }

        return $crmStatus;
    }
}
