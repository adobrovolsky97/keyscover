<?php

namespace App\Enums\Export;

enum Status: string
{
    case PENDING = 'pending';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
}
