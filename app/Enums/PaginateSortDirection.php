<?php

namespace App\Enums;

use App\Interfaces\IEnum;
use App\Traits\EnumOption;

enum PaginateSortDirection: string implements IEnum
{
    use EnumOption;

    case ASC = 'asc';
    case DESC = 'desc';

    public static function getLabel($value): string
    {
        return match ($value) {
            self::ASC => 'Ascending',
            self::DESC => 'Descending'
        };
    }
}
