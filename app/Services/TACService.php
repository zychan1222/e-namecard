<?php

namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\TACMail;
use Carbon\Carbon;

class TACService
{
    public function generateTAC()
    {
        return [
            'code' => Str::random(6),
            'expiry' => Carbon::now()->addMinutes(10),
        ];
    }

    public function sendTAC($user, $tacCode)
    {
        $user->tac_code = $tacCode['code'];
        $user->tac_expiry = $tacCode['expiry'];
        $user->save();

        Mail::to($user->email)->send(new TACMail($tacCode['code']));
    }
}
