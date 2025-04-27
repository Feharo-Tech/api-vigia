<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApiDownNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $api;
    public $statusCheck;

    public function __construct($api, $statusCheck)
    {
        $this->api = $api;
        $this->statusCheck = $statusCheck;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('API Indisponível: ' . $this->api->name)
                    ->line('A API ' . $this->api->name . ' está com problemas.')
                    ->line('URL: ' . $this->api->url)
                    ->line('Status Code: ' . $this->statusCheck->status_code)
                    ->line('Mensagem de erro: ' . $this->statusCheck->error_message)
                    ->action('Verificar API', url('/apis/' . $this->api->id));
    }
}
