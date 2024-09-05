<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory;

    protected $fillable = ['email', 'password', 'tac_code', 'tac_expiry'];

    protected $hidden = ['password'];

    public $timestamps = true;

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function socialConnections()
    {
        return $this->hasMany(SocialConnection::class);
    }
}
