<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UsuarioCriado extends Mailable
{
    use Queueable, SerializesModels;

    public $usuario;
    public $email;
    public $senha;

    /**
     * Create a new message instance.
     */
    public function __construct($usuario, $email, $senha)
    {
        $this->usuario = $usuario;
        $this->email = $email;
        $this->senha = $senha;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->usuario.', Aqui estão suas credenciais de acesso ao API Vigia',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.usuario_criado',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
