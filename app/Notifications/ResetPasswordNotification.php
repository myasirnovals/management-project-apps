<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends ResetPassword
{
    public function toMail($notifiable): MailMessage
    {
        $minutes = config('auth.passwords.'.config('auth.defaults.passwords').'.expire');

        return (new MailMessage)
            ->subject('Atur ulang kata sandi AgilePM')
            ->greeting('Halo, '.$notifiable->name)
            ->line('Kami menerima permintaan untuk mengatur ulang kata sandi akun ini.')
            ->action('Buat kata sandi baru', $this->resetUrl($notifiable))
            ->line("Tautan berlaku {$minutes} menit dan hanya bisa dipakai sekali.")
            ->line('Abaikan email ini bila Anda tidak memintanya. Kata sandi lama tetap berlaku.')
            ->salutation('Tim AgilePM');
    }
}

