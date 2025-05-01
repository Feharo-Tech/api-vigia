<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    protected $fillable = [
        'name',
        'type',
        'path',
        'original_name',
        'password',
    ];

    protected $hidden = [
        'password',
    ];

    public const TYPES = ['PFX', 'PEM'];

    public function apis()
    {
        return $this->hasMany(Api::class);
    }
}
