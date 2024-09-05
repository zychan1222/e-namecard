<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class Employee extends Authenticatable
{
    use Notifiable;
    use HasFactory;

    protected $table = 'employees';

    protected $fillable = [
        'user_id', 'name', 'name_cn', 'designation', 'phone', 'profile_pic', 'department', 'company_id', 'is_active'
    ];

    protected $hidden = [
        'remember_token',
    ];

    protected $attributes = [
        'name' => '',
        'profile_pic' => '',
        'phone' => '',
        'department' => '',
        'designation' => '',
        'is_active' => true,
    ];

    public function generateAndSendTAC()
    {
        $this->tac_code = Str::random(6);
        $this->tac_expiry = now()->addMinutes(10); // TAC valid for 10 minutes
        $this->save();

        Mail::to($this->user->email)->send(new \App\Mail\TACMail($this->tac_code));
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function socialConnections()
    {
        return $this->hasMany(SocialConnection::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'company_id');
    }

    public function admin()
    {
        return $this->hasOne(Admin::class, 'employee_id', 'id');
    }

    public function getIsOwnerAttribute()
    {
        return $this->organization && $this->id === $this->organization->owner_id;
    }
}
