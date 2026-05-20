<?php

namespace App\Enums;

use App\Traits\EnumValues;

enum TaskStatusEnum: string
{
    use EnumValues;
    case TO_DO = 'to-do';
    case IN_PROGRESS = 'in-progress';
    case COMPLETED = 'completed';

    public static function ordered(): array
    {
        return [
            self::IN_PROGRESS->value,
            self::TO_DO->value,
            self::COMPLETED->value,
        ];
    }
}
