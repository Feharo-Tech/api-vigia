<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    protected $fillable = [
        'name',
        'type',
        'path',
        'password',
    ];

    protected $hidden = [
        'password',
    ];

    public function apis()
    {
        return $this->hasMany(Api::class);
    }
}
