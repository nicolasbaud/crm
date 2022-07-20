<?php

namespace App\Notifications\UnpaidRecovery;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\UnpaidRecovery;
use Carbon\Carbon;

class SecondRelaunch extends Notification
{
    use Queueable;

    /**
     * @var object
     */
    private $unpaidRecovery;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(UnpaidRecovery $unpaidRecovery)
    {
        $this->unpaidRecovery = $unpaidRecovery;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Seconde relance d’impayé - facture #'.$this->unpaidRecovery->ref)
                    ->greeting('Madame, Monsieur,')
                    ->line('Après relance de notre part, le paiement de la facture n°'.$this->unpaidRecovery->ref.' datée du '.Carbon::parse($this->unpaidRecovery->factured_at)->format('d/m/Y').', pour un montant restant dû de '.$this->unpaidRecovery->amount.' € TTC et arrivée à échéance le '.Carbon::parse($this->unpaidRecovery->echance_at)->format('d/m/Y').', ne nous est toujours pas parvenu.')
                    ->line('Nous vous prions de bien vouloir procéder à son règlement dans les meilleurs délais.')
                    ->line('Nous tenons à vous préciser que cette relance est la dernière avant mise en demeure et recouvrement.')
                    ->line('Vous remerciant de faire le nécessaire rapidement, nous vous prions d’agréer, Madame, Monsieur, l’expression de nos salutations distinguées.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
