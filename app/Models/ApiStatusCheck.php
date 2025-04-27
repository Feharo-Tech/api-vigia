<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiStatusCheck extends Model
{
    use HasFactory;

    protected $fillable = [
        'api_id', 'status_code', 'response_time', 
        'success', 'response_body', 'error_message'
    ];

    public function api()
    {
        return $this->belongsTo(Api::class);
    }
}