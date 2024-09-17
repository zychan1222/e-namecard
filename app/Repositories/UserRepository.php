<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    public function create(array $data)
    {
        return User::create($data);
    }

    public function findByEmail(string $email)
    {
        return User::where('email', $email)->latest('created_at')->first();
    }

    public function findByEmails(array $emails)
    {
        return User::whereIn('email', $emails)->get();
    }

    public function find(int $id)
    {
        return User::find($id);
    }
    
}
