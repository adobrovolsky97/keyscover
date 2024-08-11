<?php

namespace App\Enums\Config;

/**
 * Class Type
 */
enum Type: string
{
    case INTEGER = 'integer';
    case STRING = 'string';
    case BOOLEAN = 'boolean';
    case FLOAT = 'float';

    public function getConvertedValue(mixed $value): mixed
    {
        return match ($this) {
            self::INTEGER => (int)$value,
            self::STRING => (string)$value,
            self::BOOLEAN => (bool)$value,
            self::FLOAT => (float)$value,
        };
    }
}
