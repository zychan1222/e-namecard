<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TACCode extends Model
{
    use HasFactory;

    protected $table = 'tac_codes';

    protected $fillable = [
        'email', 'tac_code', 'expires_at',
    ];

    public $timestamps = true;
}
