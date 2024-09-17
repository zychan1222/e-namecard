<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserOrganization extends Model
{
    protected $table = 'user_organization';

    public $timestamps = true;
    protected $primaryKey = ['user_id', 'organization_id'];
    public $incrementing = false;

    protected $fillable = ['user_id', 'organization_id'];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }
    
}
