<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class QueuedResetPasswordNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $token;
    public $host;

    /**
     * Create a new notification instance.
     *
     * @param string $token
     * @return void
     */
    public function __construct($token, $host )
    {
        $this->token = $token;
        $this->host = $host;

    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {

        $resetUrl = "https://{$this->host}/password/reset/{$this->token}?email=" . urlencode($notifiable->email);
//        $resetUrl = url(route('password.reset', [
//            'token' => $this->token,
//            'email' => $notifiable->email,
//        ], false));
//
        return (new MailMessage)
            ->subject('Notification de réinitialisation du mot de passe')
            ->line('Vous recevez cet e-mail parce que nous avons reçu une demande de réinitialisation de mot de passe pour votre compte.')
            ->action('Réinitialiser le mot de passe', $resetUrl)
            ->line('Si vous n\'avez pas demandé de réinitialisation de mot de passe, aucune autre action n\'est nécessaire.')
            ->line('Si vous avez des difficultés à cliquer sur le bouton "Réinitialiser le mot de passe", copiez et collez l\'URL ci-dessous dans votre navigateur web : ' . $resetUrl);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
