<?php

namespace App\Actions\Staff;

use App\Data\Staff\IssuedStaffInvitation;
use App\Notifications\StaffInvitationNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use LogicException;
use Throwable;

class SendStaffInvitation
{
    public function execute(IssuedStaffInvitation $issued): bool
    {
        $invitation = $issued->invitation;

        if (
            ! $invitation->exists
            || $invitation->getConnection()->transactionLevel() !== 0
        ) {
            throw new LogicException(
                'Invitation delivery requires a persisted invitation outside a transaction.',
            );
        }

        try {
            if (config('mail.mailers.smtp.transport') !== 'smtp') {
                throw new LogicException(
                    'Invitation delivery requires the SMTP transport.',
                );
            }

            Notification::route('mail', $invitation->email)
                ->notify(new StaffInvitationNotification($issued));

            return true;
        } catch (Throwable $exception) {
            Log::warning('staff.invitation_delivery_failed', [
                'invitation_id' => $invitation->id,
                'exception_type' => $exception::class,
            ]);

            return false;
        }
    }
}
