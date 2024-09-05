<?php

use App\Services\TACService;
use App\Mail\TACMail;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    $this->tacService = new TACService();
});

it('generates a TAC code with an expiry time', function () {
    $tac = $this->tacService->generateTAC();

    expect($tac)->toHaveKey('code');
    expect($tac['code'])->toHaveLength(6);
    expect($tac)->toHaveKey('expiry');
    expect($tac['expiry'])->toBeInstanceOf(Carbon\Carbon::class);
});

it('sends TAC code to the user and saves it', function () {
    Mail::fake();

    $user = new class {
        public $email = 'test@example.com';
        public $tac_code;
        public $tac_expiry;

        public function save() {
            return true;
        }
    };

    $tacCode = $this->tacService->generateTAC();
    $this->tacService->sendTAC($user, $tacCode);

    expect($user->tac_code)->toBe($tacCode['code']);
    expect($user->tac_expiry)->toBe($tacCode['expiry']);

    Mail::assertSent(TACMail::class, function ($mail) use ($user, $tacCode) {
        return $mail->hasTo($user->email) && $mail->tacCode === $tacCode['code'];
    });
});
