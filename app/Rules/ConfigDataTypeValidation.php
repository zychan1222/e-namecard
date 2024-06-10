<?php

namespace App\Rules;

use App\Models\Config;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;

class ConfigDataTypeValidation implements ValidationRule, DataAwareRule
{
    protected $data = [];

    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $key = $this->data['key'] ?? null;

        // if $key dont exist or $key is not in CONFIG_DATA_TYPE
        if (!$key || !array_key_exists($key, Config::CONFIG_DATA_TYPE)) {
            return;
        }

        $config_type = Config::CONFIG_DATA_TYPE[$key];

        // check Config->key is of type json
        if ($config_type === 'json' && gettype($value) !== 'array') {
            $fail(__('validation.json', ['attribute' => 'value']));
        }

        // check Config->key is of type string
        if ($config_type === 'string' && gettype($value) !== 'string') {
            $fail(__('validation.string', ['attribute' => 'value']));
        }
    }
}
