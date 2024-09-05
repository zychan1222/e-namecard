<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'address', 'email', 'phoneNo', 'owner_id', 'logo'];

    public function employees()
    {
        return $this->hasMany(Employee::class, 'company_id');
    }

    public function owner()
    {
        return $this->belongsTo(Employee::class, 'owner_id');
    }        
}