<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    use HasFactory;

    protected $fillable = ['employee_id', 'role'];

    protected $attributes = [
    'role' => '',
    ];
    
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    // Check if the user is an admin
    public function isAdmin()
    {
        return !is_null($this->employee_id);
    }
}

