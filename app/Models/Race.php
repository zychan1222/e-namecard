<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Race extends Model
{
    use HasFactory, HasTranslations;

    protected $table = 'master_races';

    public $translatable = ['name'];
    public $fillable = [
        'name'
    ];
}
