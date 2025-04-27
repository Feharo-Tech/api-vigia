<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = ['name', 'color'];

    public function apis()
    {
        return $this->belongsToMany(Api::class, 'api_tag', 'tag_id', 'api_id');
    }
}
