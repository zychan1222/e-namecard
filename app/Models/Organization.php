<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'name',
        'logo',
        'address',
        'email',
        'phoneNo',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_organization');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_organization', 'organization_id', 'role_id');
    }
}
