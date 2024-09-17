<?php

namespace App\Repositories;

use App\Models\TACCode;

class TACCodeRepository
{
    public function updateOrCreate(array $conditions, array $attributes)
    {
        return TACCode::updateOrCreate($conditions, $attributes);
    }

    public function findValidTAC(string $email, string $tac)
    {
        return TACCode::where('email', $email)
                      ->where('tac_code', $tac)
                      ->where('expires_at', '>', now())
                      ->first();
    }
}
