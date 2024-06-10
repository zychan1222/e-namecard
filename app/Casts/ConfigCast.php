<?php

namespace App\Casts;

use App\Models\Config;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class ConfigCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if ($this->configIsJson($attributes['key'])) {
            return json_decode($value, true);
        }

        return $value;
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if ($this->configIsJson($attributes['key'])) {
            return json_encode($value);
        }

        return $value;
    }

    public function configIsJson($key): bool
    {
        return !empty(Config::CONFIG_DATA_TYPE[$key]) && Config::CONFIG_DATA_TYPE[$key] === 'json';
    }
}
