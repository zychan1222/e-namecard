<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Employee extends Authenticatable
{
    use Notifiable;
    use HasFactory;


protected $table = 'employees';

protected $fillable = [
    'name', 'email', 'password', 'name_cn', 'designation', 'phone', 'profile_pic', 'department', 'company_name', 'is_active' // Add other fields as needed
];

protected $hidden = [
    'password', 'remember_token',
];

protected $attributes = [
    'company_name' => '',
    'profile_pic' => '',
    'phone' => '',
    'department' => '',
    'designation' => '',
    'is_active' => true,
];


public function socialConnections()
{
    return $this->hasMany(SocialConnection::class);
}


}