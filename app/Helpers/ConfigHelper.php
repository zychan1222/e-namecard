<?php

namespace App\Helpers;

use App\Repositories\ConfigRepository;
use Illuminate\Support\Facades\Cache;

class ConfigHelper
{
    public static function get(mixed $key): mixed
    {
        $config_repository = resolve(ConfigRepository::class);

        return Cache::tags(['config'])->rememberForever($key, function () use ($key, $config_repository) {
            return $config_repository->getConfigByKey($key)?->value;
        });
    }

    public static function put(mixed $key, mixed $value): mixed
    {
        $config_repository = resolve(ConfigRepository::class);
        $config = $config_repository->updateOrCreate(['key' => $key], ['value' => $value]);

        Cache::tags(['config'])->put($config->key, $config->value);

        return self::get($config->key);
    }

    public static function delete(mixed $key): void
    {
        $config_repository = resolve(ConfigRepository::class);
        $config = $config_repository->getConfigByKey($key);

        if($config) {
            if($config_repository->delete($config)) {
                Cache::tags(['config'])->forget($key);
            }
        }
    }
}
