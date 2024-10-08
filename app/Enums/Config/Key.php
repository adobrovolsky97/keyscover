<?php

namespace App\Enums\Config;

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
}
