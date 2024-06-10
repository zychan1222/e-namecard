<?php

namespace App\Interfaces;

interface IEnum
{
    public static function values(): array;

    public function label(): string;

    public static function getLabel($value): string;
}
