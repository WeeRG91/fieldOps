<?php

namespace App\Notifications;

use App\Data\Staff\IssuedStaffInvitation;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StaffInvitationNotification extends Notification
{
    /**
     * Create a new notification instance.
     */
    public function __construct(
        private readonly IssuedStaffInvitation $issued,
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $invitation = $this->issued->invitation;

        $acceptUrl = route('invitations.accept.show', [
            'invitation' => $invitation->id,
        ]).'#token='.rawurlencode($this->issued->tokenForDelivery());

        return (new MailMessage)
            ->mailer('smtp')
            ->subject('FieldOps staff invitation')
            ->greeting('Hello!')
            ->line('You have been invited to join FieldOps.')
            ->line('This invitation is for '.$invitation->email.'.')
            ->action('Accept invitation', $acceptUrl)
            ->line(
                'This invitation expires at '.
                $invitation->expires_at->utc()->format('Y-m-d H:i:s').
                ' UTC, 48 hours after issuance.',
            )
            ->line(
                'Use this link to choose your own password. '.
                'FieldOps does not include passwords in invitation emails.',
            )
            ->line(
                'Do not share this invitation link. '.
                'If you were not expecting it, contact the person who invited you.',
            );
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
