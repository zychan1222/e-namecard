<?php
// app/Models/SocialConnection.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialConnection extends Model
{
    protected $fillable = [
        'employee_id', 'provider', 'provider_id', 'access_token',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
?>