<?php

namespace App\Data\Staff;

use App\Models\StaffInvitation;
use JsonSerializable;
use SensitiveParameter;
use SensitiveParameterValue;

final readonly class IssuedStaffInvitation implements JsonSerializable
{
    private SensitiveParameterValue $secret;

    public function __construct(
        public StaffInvitation $invitation,
        #[SensitiveParameter] string $token,
    ) {
        $this->secret = new SensitiveParameterValue($token);
    }

    public function tokenForDelivery(): string
    {
        return (string) $this->secret->getValue();
    }

    /**
     * @return array<string, int|string>
     */
    public function __debugInfo(): array
    {
        return [
            'invitation_id' => $this->invitation->id,
            'token' => '[redacted]',
        ];
    }

    /**
     * @return array{invitation_id: int}
     */
    public function jsonSerialize(): array
    {
        return [
            'invitation_id' => $this->invitation->id,
        ];
    }
}
