<?php

namespace App\Notifications\UnpaidRecovery;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\UnpaidRecovery;
use Carbon\Carbon;

class FirstRelaunch extends Notification
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
                    ->greeting('Madame, Monsieur,')
                    ->line('Sauf erreur ou omission de notre part, le paiement de la facture n°'.$this->unpaidRecovery->ref.' datée du '.Carbon::parse($this->unpaidRecovery->factured_at)->format('d/m/Y').', pour un montant restant dû de '.$this->unpaidRecovery->amount.' € TTC et arrivée à échéance le '.Carbon::parse($this->unpaidRecovery->echance_at)->format('d/m/Y').', ne nous est pas parvenu.')
                    ->line('Nous vous prions de bien vouloir procéder à son règlement dans les meilleurs délais, et vous adressons, à toutes fins utiles.')
                    ->line('Si par ailleurs votre paiement venait à nous parvenir avant la réception de cette relance, nous vous saurions gré de ne pas tenir compte de cette dernière.')
                    ->line('Vous remerciant de faire le nécessaire, et restant à votre entière disposition pour toute éventuelle question, nous vous prions d’agréer, Madame, Monsieur, l’expression de nos salutations distinguées.');
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
