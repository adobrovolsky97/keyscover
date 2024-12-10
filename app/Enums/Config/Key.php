<?php

namespace App\Enums\Config;

use Illuminate\Validation\Rule;

/**
 * Class Key
 */
enum Key: string
{
    case DOLLAR = 'dollar';
    case FIVE_PERCENT_DISCOUNT_SUM = 'five_percent_discount_sum';
    case TEN_PERCENT_DISCOUNT_SUM = 'ten_percent_discount_sum';
    case FREE_DELIVERY_SUM = 'free_delivery_sum';
    case IS_CRM_ENABLED = 'is_crm_enabled';
    case SYNC_CRM_FIELD = 'sync_crm_field';

    /**
     * @return array|string[]
     */
    public function getValidationRules(): array
    {
        return match ($this) {
            self::IS_CRM_ENABLED => ['required', 'boolean'],
            self::SYNC_CRM_FIELD => ['required', 'string', Rule::in(['sku', 'external_id'])],
        };
    }
}
