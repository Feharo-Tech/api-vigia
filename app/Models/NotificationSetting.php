<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'email_notifications', 'notification_email', 'notification_frequency', 'last_notification_sent_at'
    ];

    protected $casts = [
        'last_notification_sent_at' => 'datetime',
    ];

    public const FREQUENCIES_INTERVALS = [
        1 => '1 minuto',
        5 => '5 minutos',
        10 => '10 minutos',
        15 => '15 minutos',
        30 => '30 minutos',
        60 => '1 hora',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
