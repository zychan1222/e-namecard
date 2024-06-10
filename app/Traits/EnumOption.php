<?php

namespace App\Traits;

trait EnumOption
{
    /**
     * @return array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * @param bool $withNull
     * @return array
     */
    public static function options(bool $withNull = false): array
    {
        $data = [];
        if ($withNull) {
            $data[] = [
                'value' => null,
                'name' => 'Please select one.'
            ];
        }

        foreach (self::cases() as $item) {
            $data[] = [
                'value' => $item->value,
                'name' => self::getLabel($item)
            ];
        }
        return $data;
    }


    /**
     * @return string
     */
    public function label(): string
    {
        return static::getLabel($this);
    }

}
