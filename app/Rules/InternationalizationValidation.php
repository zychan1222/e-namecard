<?php

namespace App\Rules;

use App\Repositories\InternationalizationRepository;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class InternationalizationValidation implements ValidationRule
{
    private InternationalizationRepository $internationalizationRepository;

    public function __construct()
    {
        $this->internationalizationRepository = resolve(InternationalizationRepository::class);
    }
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $locale_code = explode('.', $attribute)[1];

        $exists = $this->internationalizationRepository->getActiveInternationalizationByCode($locale_code);

        if (!$exists) {
            $fail(__('validation.locale_not_supported', ['attribute' => $locale_code]));
        }
    }
}
